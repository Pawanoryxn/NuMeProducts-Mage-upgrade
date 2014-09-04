<?php
/**
 * @category   Oro
 * @package    Oro_SalesRule
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Oro Observer class
 */
class Oro_SalesRule_Model_Observer
{
    /**
     * Add custom cart rule conditions
     *
     * @param Varien_Event_Observer $observer
     */
    public function addConditions(Varien_Event_Observer $observer)
    {
        $conditions = $observer->getEvent()->getAdditional()->getConditions();

        if (!is_array($conditions)) {
            $conditions = array();
        }

        $conditions[] = array(
            'label' => Mage::helper('oro_salesrule')->__('Price Rule'),
            'value' => 'oro_salesrule/condition_rule'
        );
        $conditions[] = array(
            'label' => Mage::helper('oro_salesrule')->__('Grand Total (subtotal subtracted by any discounts)'),
            'value' => Oro_SalesRule_Model_Condition_Grandtotal::CONDITION_TYPE
        );;

        $observer->getEvent()->getAdditional()->setConditions($conditions);
    }
}
