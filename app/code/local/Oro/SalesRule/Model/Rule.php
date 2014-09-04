<?php
/**
 * @category   Oro
 * @package    Oro_SalesRule
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Rewrite of Mage_SalesRule_Model_Rule class. Some custom logic added required by custom cart rule conditions
 */
class Oro_SalesRule_Model_Rule extends Mage_SalesRule_Model_Rule
{

    /**
     * Set validation result for specific address to results cache
     * Don't cache the results if we have discount conditions, results should be recalculated for such rules every time
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @param   bool $validationResult
     * @return  Mage_SalesRule_Model_Rule
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        if (Mage::helper('oro_salesrule')->ruleHasDiscountCondition($this)) {
            return $this;
        }

        return parent::setIsValidForAddress($address, $validationResult);
    }
} 
