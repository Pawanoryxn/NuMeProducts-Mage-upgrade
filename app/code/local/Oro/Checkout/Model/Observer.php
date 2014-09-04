<?php
/**
 * @category   Oro
 * @package    Oro_Checkout
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Oro Checkout Observer class
 */
class Oro_Checkout_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function saveAttributeData(Varien_Event_Observer $observer)
    {
        $attributeId = $observer->getEvent()->getData('attribute')->getAttributeId();
        $request = Mage::app()->getRequest();
        if ($data = $request->getParam('cart_options')) {
            if (is_array($data)) {
                $optionData = array();
                foreach ($data as $optionId => $cartOption) {
                    $optionData[] = array(
                        'option_id'    => $optionId,
                        'cart_label'   => $cartOption['cart_label'],
                        'hex_color'    => $cartOption['hex_color'],
                    );
                }
                Mage::getResourceModel('oro_checkout/attribute_option')->saveOptions($optionData);
            }
        }
    }
}
