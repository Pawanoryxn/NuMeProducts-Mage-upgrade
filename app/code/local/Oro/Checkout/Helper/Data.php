<?php
/**
 * @category   Oro
 * @package    Oro_Checkout
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Oro Checkout Helper class
 */
class Oro_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var array
     */
    protected $_attributeOptionCartColors = array();

    public function getAttributeOptionCartColors($attributeId)
    {
        if (empty($this->_attributeOptionCartColors[$attributeId])) {
            /** @var $optionCollection Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection */
            $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->addFieldToSelect('option_id')
                ->setAttributeFilter($attributeId);
            Mage::getResourceModel('oro_checkout/attribute_option')->addCartLabelsInfo($optionCollection);
            $cartOptions = array();
            foreach ($optionCollection as $option) {
                $cartOptions[$option->getOptionId()] = array(
                    'label' => $option->getCartLabel(),
                    'hex_color' => $option->getHexColor()
                );
            }
            $this->_attributeOptionCartColors[$attributeId] = $cartOptions;
        }

        return $this->_attributeOptionCartColors[$attributeId];
    }
}
