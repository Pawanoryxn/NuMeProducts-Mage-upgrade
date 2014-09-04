<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


class Demac_Atfeed_Model_Feed_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('atfeed/feed_condition_combine');
    }


    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
            Mage::helper('atfeed')->__('Assign products to categories based on the following conditions: ', $this->getAggregatorElement()->getHtml(), $this->getValueElement()->getHtml());
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    public function asString($format='')
    {
        $str = Mage::helper('atfeed')->__("If %s of these conditions are %s:", $this->getAggregatorName(), $this->getValueName());
        return $str;
    }

//    public function asArray(array $arrAttributes = array())
//    {
//        $out = parent::asArray();
//        $out['aggregator'] = $this->getAggregator();
//        $out['feedcategory'] = $this->getFeedcategory();
//
//        foreach ($this->getConditions() as $condition) {
//            $out['conditions'][] = $condition->asArray();
//        }
//
//        return $out;
//    }
//
//    public function loadArray($arr, $key='conditions')
//    {
//        $this->setAggregator(isset($arr['aggregator']) ? $arr['aggregator']
//            : (isset($arr['attribute']) ? $arr['attribute'] : null))
//            ->setValue(isset($arr['value']) ? $arr['value']
//                : (isset($arr['operator']) ? $arr['operator'] : null));
//
//        if(isset($arr['feedcategory'])){
//            $this->setFeedcategory($arr['feedcategory']);
//        }
//
//        if (!empty($arr[$key]) && is_array($arr[$key])) {
//            foreach ($arr[$key] as $condArr) {
//                try {
//                    $cond = $this->_getNewConditionModelInstance($condArr['type']);
//                    if ($cond) {
//                        $this->addCondition($cond);
//                        $cond->loadArray($condArr, $key);
//                    }
//                } catch (Exception $e) {
//                    Mage::logException($e);
//                }
//            }
//        }
//        return $this;
//    }

    public function getNewChildSelectOptions()
    {
//        $addressCondition = Mage::getModel('atfeed/feed_condition_address');
//        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
//        $attributes = array();
//        foreach ($addressAttributes as $code=>$label) {
//            $attributes[] = array('value'=>'atfeed/feed_condition_address|'.$code, 'label'=>$label);
//        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'atfeed/feed_condition_product_found', 'label'=>Mage::helper('atfeed')->__('Product attribute combination')),
//            array('value'=>'atfeed/feed_condition_product_subselect', 'label'=>Mage::helper('atfeed')->__('Products subselection')),
//            array('value'=>'atfeed/feed_condition_combine', 'label'=>Mage::helper('atfeed')->__('Conditions combination')),
//            array('label'=>Mage::helper('salesrule')->__('Cart Attribute'), 'value'=>$attributes),
        ));

        $additional = new Varien_Object();
        Mage::dispatchEvent('atfeed_feed_condition_combine', array('additional' => $additional));

        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }

    /**
     * Get condition combine resource model
     *
     * @return Enterprise_CustomerSegment_Model_Mysql4_Segment
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('atfeed/feed');
    }

    /**
     * Get filter by customer condition for segment matching sql
     *
     * @param mixed $customer
     * @param string $fieldName
     * @return string
     */
    protected function _createProductFilter($product, $fieldName)
    {
        $productFilter = '';
        if ($product) {
            $productFilter = "{$fieldName} = :product_id";
        } else {
            $productFilter = "{$fieldName} = root.entity_id";
        }

        return $productFilter;
    }

    /**
     * Build query for matching customer to segment condition
     *
     * @param $customer
     * @param $website
     * @return Varien_Db_Select
     */
    protected function _prepareConditionsSql($product, $website)
    {
        $select = $this->getResource()->createSelect();
        $table = $this->getResource()->getTable('catalog/product');
        $select->from($table, array(new Zend_Db_Expr(1)));
        $select->where($this->_createProductFilter($product, 'entity_id'));
        return $select;
    }

    /**
     * Check if condition is required. It affect condition select result comparison type (= || <>)
     *
     * @return bool
     */
    protected function _getRequiredValidation()
    {
        return ($this->getValue() == 1);
    }

    /*
     * Get information if condition is required
     *
     * @return bool
     */
    public function getIsRequired()
    {
        return $this->_getRequiredValidation();
    }

    /**
     * Collect validated attributes for Product Collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection
     * @return Enterprise_TargetRule_Model_Rule_Condition_Combine
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }


    /**
     * Get information if it's used as a child of History or List condition
     *
     * @return bool
     */
    public function getCombineProductCondition()
    {
        return $this->_combineProductCondition;
    }

    /**
     * Get SQL select for matching customer to segment condition
     *
     * @param Mage_Customer_Model_Customer | Zend_Db_Select | Zend_Db_Expr $customer
     * @param int | Zend_Db_Expr $website
     * @return Varien_Db_Select
     */
    public function getConditionsSql($product, $website)
    {
        /**
         * Build base SQL
         */
        $select     = $this->_prepareConditionsSql($product, $website);
        $required   = $this->_getRequiredValidation();
        $aggregator = ($this->getAggregator() == 'all') ? ' AND ' : ' OR ';
        $operator   = $required ? '=' : '<>';
        $conditions = array();

        /**
         * Add children subselects conditions
         */
        $adapter = $this->getResource()->getReadConnection();
        foreach ($this->getConditions() as $condition) {
            if ($sql = $condition->getConditionsSql($product, $website)) {
                $isnull = $adapter->getCheckSql($sql, 1, 0);
                if ($condition->getCombineProductCondition()) {
                    $sqlOperator = $condition->getIsRequired() ? '=' : '<>';
                } else {
                    $sqlOperator = $operator;
                }
                $conditions[] = "($isnull {$sqlOperator} 1)";
            }
        }

        /**
         * Process combine subfilters. Subfilters are part of base select which cah be affected by children.
         */
        $subfilters = array();
        $subfilterMap = $this->_getSubfilterMap();
        if ($subfilterMap) {
            foreach ($this->getConditions() as $condition) {
                $subfilterType = $condition->getSubfilterType();
                if (isset($subfilterMap[$subfilterType])) {
                    $condition->setCombineProductCondition($this->_combineProductCondition);
                    $subfilter = $condition->getSubfilterSql($subfilterMap[$subfilterType], $required, $website);
                    if ($subfilter) {
                        $conditions[] = $subfilter;
                    }
                }
            }
        }

        if (!empty($conditions)) {
            $select->where(implode($aggregator, $conditions));
        }

        return $select;
    }

    /**
     * Get infromation about subfilters map. Map contain children condition type and associated
     * column name from itself select.
     * Example: array('my_subtype'=>'my_table.my_column')
     * In practice - date range can be as subfilter for different types of condition combines.
     * Logic of this filter apply is same - but column names different
     *
     * @return array
     */
    protected function _getSubfilterMap()
    {
        return array();
    }

    /**
     * Limit select by website with joining to store table
     *
     * @param Zend_Db_Select $select
     * @param int | Zend_Db_Expr $website
     * @param string $storeIdField
     * @return Enterprise_CustomerSegment_Model_Condition_Abstract
     */
    protected function _limitByStoreWebsite(Zend_Db_Select $select, $website, $storeIdField)
    {
        $storeTable = $this->getResource()->getTable('core/store');
        $select->join(array('store'=> $storeTable), $storeIdField.'=store.store_id', array())
            ->where('store.website_id=?', $website);
        return $this;
    }

}
