<?php
/**
 * @category   Oro
 * @package    Oro_Checkout
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_Checkout_Model_Resource_Attribute_Option extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define Resource Table
     */
    public function _construct()
    {
        $this->_init('oro_checkout/attribute_cart_option', 'option_id');
        $this->_isPkAutoIncrement = false;
    }

    /**
     * @param $options
     */
    public function saveOptions($options)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->insertOnDuplicate($this->getMainTable(), $options, array('cart_label', 'hex_color'));
    }

    /**
     * @param $optionCollection
     */
    public function addCartLabelsInfo(Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection $optionCollection)
    {
        $optionCollection->getSelect()->joinLeft(array('co' => $this->getMainTable()),
            'main_table.option_id = co.option_id', array('cart_label', 'hex_color')
        );
    }
}
