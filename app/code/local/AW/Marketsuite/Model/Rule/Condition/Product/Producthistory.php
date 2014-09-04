<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Marketsuite
 * @version    2.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Marketsuite_Model_Rule_Condition_Product_Producthistory extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('marketsuite/rule_condition_product_producthistory')->setValue(null);
    }

    public function getNewChildSelectOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()
            ->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            if (!$attribute->isAllowedForRuleCondition() || !$attribute->getIsUsedForPromoRules()) {
                continue;
            }

            if (Mage::helper('marketsuite')->checkUselessProductAttributes($attribute->getAttributeCode())) {
                continue;
            }

            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $attributeListForRule = array();
        foreach ($attributes as $code => $label) {
            $attributeListForRule[] = array(
                'value' => 'marketsuite/rule_condition_product_producthistory_conditions|' . $code,
                'label' => $label,
            );
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, $attributeListForRule);

        /* Add category as attribute to product attributes */
        $conditions[] = array(
            'value' => 'marketsuite/rule_condition_product_producthistory_conditions|category',
            'label' => Mage::helper('marketsuite')->__('Category')
        );

        $conditions = Mage::helper('marketsuite')->sortConditionListByLabel($conditions);
        return $conditions;
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function loadAttributeOptions()
    {
        $hlp = Mage::helper('salesrule');
        $this->setAttributeOption(
            array(
                 'viewed'  => $hlp->__('viewed'),
                 'ordered' => $hlp->__('ordered'),
            )
        );
        return $this;
    }

    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            array(
                 '==' => Mage::helper('rule')->__('for'),
                 '>'  => Mage::helper('rule')->__('more than'),
                 '<'  => Mage::helper('rule')->__('less than'),
            )
        );
        return $this;
    }

    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
            Mage::helper('salesrule')->__(
                "If Product was %s %s %s times and matches %s of these conditions:",
                $this->getAttributeElement()->getHtml(),
                $this->getOperatorElement()->getHtml(),
                $this->getValueElement()->getHtml(),
                $this->getAggregatorElement()->getHtml()
            );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    public function validate(Varien_Object $object)
    {
        if ($object instanceof Mage_Sales_Model_Order && $object->getId()) {
            if ($this->getAttribute() == 'viewed') {
                return false;
            }
            if ($this->getAttribute() == 'ordered') {

                if ($object->getState() != Mage_Sales_Model_Order::STATE_COMPLETE) {
                    return false;
                }

                $totalOrderedCount = 0;
                foreach ($object->getAllItems() as $item) {
                    $product = Mage::getModel('catalog/product')->load($item->getProductId());
                    if ($this->validateProduct($product)) {
                        $totalOrderedCount += $item->getData('qty_ordered');
                    }
                }
                return $this->validateAttribute($totalOrderedCount);
            }
        }

        if ($object instanceof Mage_Customer_Model_Customer && $object->getId()) {
            if ($this->getAttribute() == 'viewed') {
                $totalViewedCount = 0;
                $productList = Mage::helper('marketsuite/customer')->getProductListViewedByCustomer($object);
                foreach ($productList as $product) {
                    if ($this->validateProduct($product)) {
                        $totalViewedCount += $product->getViewsCount();
                    }
                }
                return $this->validateAttribute($totalViewedCount);
            }
            if ($this->getAttribute() == 'ordered') {
                $totalOrderedCount = 0;
                $customersOrders = Mage::helper('marketsuite/customer')->getOrderCollectionByCustomer($object);

                foreach ($customersOrders as $order) {
                    if ($order->getState() != Mage_Sales_Model_Order::STATE_COMPLETE) {
                        continue;
                    }

                    foreach ($order->getAllItems() as $item) {
                        $product = $product = Mage::getModel('catalog/product')->load($item->getProductId());
                        if ($this->validateProduct($product)) {
                            $totalOrderedCount += $item->getData('qty_ordered');
                        }
                    }
                }
                return $this->validateAttribute($totalOrderedCount);
            }
        }

        return false;
    }

    public function validateProduct($product)
    {
        if (!$this->getConditions()) {
            return true;
        }

        $all    = $this->getAggregator() === 'all';

        foreach ($this->getConditions() as $condition) {
            $validated = $condition->validate($product);

            if ($all && $validated !== true) {
                return false;
            } elseif (!$all && $validated === true) {
                return true;
            }
        }
        return $all ? true : false;
    }

    public function getQuery($query)
    {
        foreach ($this->getConditions() as $cond) {
            $query = $cond->getQuery($query);
        }
        return $query;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}