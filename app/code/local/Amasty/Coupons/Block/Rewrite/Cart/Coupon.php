<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Coupons
*/
class Amasty_Coupons_Block_Rewrite_Cart_Coupon extends Mage_Checkout_Block_Cart_Coupon
{
    protected function _toHtml()
    {
        // $this->setTemplate('amcoupon/checkout/cart/coupon.phtml');
        return parent::_toHtml();
    }
    
    public function getAppliedCoupons()
    {
    	// my code
        $userRdirectionValidation = Mage::getSingleton('core/session')->getspecialdiscountPer();
       // if($userRdirectionValidation  != "")
        // {
        $appliedCoupons = $this->getQuote()->getAppliedCoupons();
        // print_r($appliedCoupons); 
        $couponsize = sizeof($appliedCoupons);
        if($couponsize == 2)
        {
        // remove coupon codes
        $coupon_codes = '';
        echo '<div style="display:none;">';
        Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($coupon_codes)->collectTotals()->save();
        echo '</div>';
        // remove coupon codes
        $followupCount = 0;
        $generalCouponCount = 0;
        foreach($appliedCoupons as $xfCouponCode)
        {
            $oCoupon = Mage::getModel('salesrule/coupon')->load($xfCouponCode, 'code');
            $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
            $xfdiscountCode = $oRule->getData();
            if($xfdiscountCode['coupon_type'] == 3)
            {
                if($followupCount < 1)
                    {
                echo '<div style="display:none;">';
                Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($xfCouponCode)->collectTotals()->save();
                echo '</div>';
                $followupCount = $followupCount  +1;
                    }
            }else
            {
                if($generalCouponCount < 1)
                {
                echo '<div style="display:none;">';
                Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($xfCouponCode)->collectTotals()->save();
                echo '</div>';
                $generalCouponCount = $generalCouponCount + 1;
                }
            }
        }
    }elseif($couponsize > 2)
    {
        // remove coupon codes
        $coupon_codes = '';
        echo '<div style="display:none;">';
        Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($coupon_codes)->collectTotals()->save();
        echo '</div>';
        // remove coupon codes
        $followupCount = 0;
        $generalCouponCount = 0;
        foreach($appliedCoupons as $xfCouponCode)
        {
            $oCoupon = Mage::getModel('salesrule/coupon')->load($xfCouponCode, 'code');
            $oRule = Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId()); 
            $xfdiscountCode = $oRule->getData();
            if($xfdiscountCode['coupon_type'] == 3)
            {
                if($followupCount < 1)
                    {
                echo '<div style="display:none;">';
                Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($xfCouponCode)->collectTotals()->save();
                $followupCount = $followupCount  +1;
                echo '</div>'; 
                    }
            }else 
            {
                if($generalCouponCount < 1)
                {
                echo '<div style="display:none;">';
                Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($xfCouponCode)->collectTotals()->save();
                $generalCouponCount = $generalCouponCount + 1;
                echo '</div>';
                }
            }
        }
    }
// }
/*else
{
        $appliedCoupons = $this->getQuote()->getAppliedCoupons();
        $couponsize = sizeof($appliedCoupons);
        if($couponsize >= 2)
        {
            $coupon_codes = '';
            Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($coupon_codes)->collectTotals()->save();
            $couponsizeApply = $couponsize-1;
            $xfCouponCode = $appliedCoupons[ 0 ];
            Mage::getSingleton('checkout/cart')->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($xfCouponCode)->collectTotals()->save();
        }
}*/

            $cart = Mage::getSingleton('checkout/session')->getQuote();
            foreach ($cart->getAllAddresses() as $address) 
		{
		   $cart->unsetData('cached_items_nonnominal');
		   $cart->unsetData('cached_items_nominal');
		}
		$cart->setTotalsCollectedFlag(false);
		$cart->collectTotals();

      return $this->getQuote()->getAppliedCoupons();
    }
} 