<?php

class Demac_Atfeed_Model_Feed extends Demac_Atfeed_Model_Abstract {

    protected $_productCollection = null;
    protected $_categoryCollection = null;
    protected $_parentProductsCache = array();
    protected $_store = null;
    protected $product_image_width = null;
    protected $product_image_height = null;
    protected $_isDeleteable = true;
    protected $_isReadonly = false;
    protected $_storeId = null;

    /**
     * Feed statuses
     */
    const STATUS_ERROR              =  0;
    const STATUS_SUCCESSFUL         =  1;
    const STATUS_RUNNING            =  2;
    const STATUS_WARNING            =  3;

    /**
     * Store matched products objects
     *
     * @var array
     */
    protected $_products;

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;


    public function _construct() {
        parent::_construct();
        $this->_init('atfeed/feed');
    }

    /**
     * Getter for rule combine conditions instance
     *
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('atfeed/feed_condition_combine');
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return Mage_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return Mage::getModel('atfeed/feed_condition_product_combine');
    }



    /**
     * Check availabitlity to delete model
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deleteable flag
     *
     * @param boolean $flag
     * @return Mage_Rule_Model_Rule
     */
    public function setIsDeleteable($flag)
    {
        $this->_isDeleteable = (bool) $flag;
        return $this;
    }


    /**
     * Checks model is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is readonly flag
     *
     * @param boolean $value
     * @return Mage_Rule_Model_Rule
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (boolean) $value;
        return $this;
    }


    /**
     * Retrieve array of product objects which are matched by rule
     *
     * @param $onlyId bool
     *
     * @return Enterprise_TargetRule_Model_Rule
     */
    public function prepareMatchingProducts($specialAttributes,$onlyId = false)
    {
        $storeId = Mage::getModel('core/website')->load($this->getWebsiteId())
            ->getDefaultGroup()
            ->getDefaultStoreId();

        $this->_storeId = $storeId;

        $productCollection = Mage::getModel('catalog/product')->setStoreId($storeId)->getCollection()->addStoreFilter($storeId);//Mage::getResourceModel('catalog/product_collection');
        $productCollection
            ->addAttributeToFilter('visibility', array('in' => array(3,4)))
            ->addAttributeToFilter('status', 1)

        ;
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($productCollection);


        if (!$onlyId && !is_null($this->_productIds)) {
            $productCollection->addIdFilter($this->_productIds);
            $this->_products = $productCollection->getItems();
        } else {
            $this->setCollectedAttributes(array());
            $this->getActions()->collectValidatedAttributes($productCollection);
            $this->_collectSpecialAttributes($productCollection,$specialAttributes);

            $this->_productIds = array();
            $this->_products   = array();

            Mage::getSingleton('core/resource_iterator')->walk(
                $productCollection->getSelect(),
                array(
                    array($this, 'callbackValidateProduct')
                ),
                array(
                    'attributes'    => $this->getCollectedAttributes(),
                    'product'       => Mage::getModel('catalog/product'),
                    'onlyId'        => (bool) $onlyId
                )
            );
        }

        return $this;
    }

    protected function _collectSpecialAttributes($productCollection,$specialAttributes)
    {
        foreach ($specialAttributes as $attribute) {
            $attribute = Mage::getModel('eav/entity_attribute')->load($attribute['attribute_id']);
            if ('category_ids' != $attribute['attribute_id']) {
                $attributes = $this->getCollectedAttributes();
                $attributes[$attribute->getAttributeCode()] = true;
                $this->setCollectedAttributes($attributes);
                $productCollection->addAttributeToSelect($attribute->getAttributeCode(), 'left');
                if ('sku' != $attribute['attribute_code']) {
                    $this->_entityAttributeValues = $productCollection->getAllAttributeValues($attribute->getAttributeCode());
                }
            }
        }

        return $this;
    }


    /**
     * Retrieve array of product objects which are matched by rule
     *
     * @deprecated
     *
     * @return array
     */
    public function getMatchingProducts($specialAttributes)
    {
        if (is_null($this->_products)) {
            $this->prepareMatchingProducts($specialAttributes);
        }

        return $this->_products;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        $object = new Varien_Object();
        $object->setProduct($product);

        if ($this->getActions()->validate($object)) {
            $this->_productIds[] = $product->getId();
            if (!key_exists('onlyId', $args) || !$args['onlyId']) {
                $this->_products[] = $product;
            }
        }
    }

    /**
     * Retrieve array of product Ids that are matched by rule
     *
     * @return array
     */
    public function getMatchingProductIds()
    {
        if (is_null($this->_productIds)) {
            $this->getMatchingProducts(array());
        }

        return $this->_productIds;
    }


    public function getStoreId()
    {
        return $this->_storeId;
    }


    public function getProductCollection($specialAttributes = array())
    {
       $collection = $this->getMatchingProducts($specialAttributes);

       return $collection;
    }
}