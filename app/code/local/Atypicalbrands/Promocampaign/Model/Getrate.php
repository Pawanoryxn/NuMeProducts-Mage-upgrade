<?php

class Atypicalbrands_Promocampaign_Model_Getrate extends Mage_Core_Model_Abstract {

    /**
     * Rule source collection
     *
     * @var Mage_SalesRule_Model_Mysql4_Rule_Collection
     */
    protected $_rules;
    protected $key;

    function getRate() {

        $couponCode = Mage::getSingleton('checkout/cart')->getQuote()->getCouponCode();
        $Coupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
        $rule = Mage::getModel('salesrule/rule')->load($Coupon->getRuleId());

        if ($rule && $rule->getCouponShippingRate()) {

            $cart = Mage::getSingleton('checkout/cart');
            $a = $cart->getQuote()->getShippingAddress();
            $shippingCountry = $a->getCountry();
            $countries = unserialize($rule->getCouponShippingRate());

            if (!empty($countries) && array_key_exists($shippingCountry, $countries)) {

                return ($countries[$shippingCountry]);
            }
        }

        return NULL;
    }

}
