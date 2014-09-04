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
 * Discount link controller
 */
class Crius_DiscountLink_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Redirect coupon URL (coupon code is saved in observer)
     */
	public function setCouponAction()
	{
		if ($couponcode = $this->getRequest()->getParam('couponcode')) {
		    if ($rule = Mage::helper('discountlink')->getSalesRuleFromCouponCode($couponcode)) {
		        if ($url = $rule->getCouponRedirectUrl()) {
		            $this->_redirectUrl($url);
		            return;
		        }
		    }
		}
		$this->_redirect('/');
	}
}