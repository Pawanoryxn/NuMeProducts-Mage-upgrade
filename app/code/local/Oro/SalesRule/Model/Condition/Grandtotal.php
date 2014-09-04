<?php
/**
 * @category   Oro
 * @package    Oro_SalesRule
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Grandtotal condition (the subtotal subtracted by any discounts without including shipping, tax or any other fees)
 */
class Oro_SalesRule_Model_Condition_Grandtotal extends Mage_Rule_Model_Condition_Abstract
{
    const CONDITION_TYPE = 'oro_salesrule/condition_grandtotal';

    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * Render element HTML
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('oro_salesrule')->__(
                'Grand Total %s %s',
                $this->getOperatorElementHtml(),
                $this->getValueElement()->getHtml()
            )
            . $this->getRemoveLinkHtml();
    }

    /**
     * Validate rule
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  bool
     */
    public function validate(Varien_Object $address)
    {
        return $this->validateAttribute($address->getBaseSubtotalWithDiscountOrigin());
    }
}
