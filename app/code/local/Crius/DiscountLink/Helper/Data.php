<?php
/**
 * Crius
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt
 *
 * @category   Crius
 * @package    Crius_DiscountLink
 * @copyright  Copyright (c) 2013 Crius (http://www.criuscommerce.com)
 * @license    http://www.criuscommerce.com/CRIUS-LICENSE.txt
 */
/**
 * Discount link helper
 */
class Crius_DiscountLink_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Check if string is valid coupon code
     *
     * @param string $couponcode
     * @return boolean
     */
    public function validateCouponCode($couponcode)
    {
        if (version_compare(Mage::getVersion(), '1.4.1.0', '>=')) {
            // Magento 1.4.1+
            $coupon = Mage::getModel('salesrule/coupon');
            $coupon->load($couponcode, 'code');
            return (bool)($coupon->getId());
        } else {
            // Magento <1.4.1
            $rules = Mage::getResourceModel('salesrule/rule_collection');
            $rules->addBindParam('coupon_code', $couponcode);
            $rules->getSelect()->where("coupon_code=:coupon_code");
            $rules->getSelect()->where('is_active=1');
            $now = Mage::getModel('core/date')->date('Y-m-d');
            $rules->getSelect()->where('from_date is null or from_date<=?', $now);
            $rules->getSelect()->where('to_date is null or to_date>=?', $now);
            return (bool)($rules->getSize());
        }
    }
    
    /**
     * Get sales rule object from coupon code
     *
     * @param string $couponcode
     * @return Mage_SalesRule_Model_Rule|null
     */
    public function getSalesRuleFromCouponCode($couponcode)
    {
        if (version_compare(Mage::getVersion(), '1.4.1.0', '>=')) {
            // Magento 1.4.1+
            $coupon = Mage::getModel('salesrule/coupon');
            $coupon->load($couponcode, 'code');
            if ($coupon) {
                return Mage::getModel('salesrule/rule')->load($coupon->getRuleId());
            }
        } else {
            // Magento <1.4.1
            $rules = Mage::getResourceModel('salesrule/rule_collection');
            $rules->addBindParam('coupon_code', $couponcode);
            $rules->getSelect()->where("coupon_code=:coupon_code");
            $rules->getSelect()->where('is_active=1');
            $now = Mage::getModel('core/date')->date('Y-m-d');
            $rules->getSelect()->where('from_date is null or from_date<=?', $now);
            $rules->getSelect()->where('to_date is null or to_date>=?', $now);
            if ($rules->getSize()) {
                return $rules->getFirstItem();
            }
        }
        return null;
    }
}