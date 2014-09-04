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
 * Observer model
 */
class Crius_DiscountLink_Model_Observer
{
    /**
     * Check URL parameter for coupon code and remember it in session for later use when a quote is created
     *
     * @param Varien_Event_Observer $observer
     * @return Crius_DiscountLink_Model_Observer
     **/
    public function setCoupon($observer)
    {
        $controller = $observer->getEvent()->getControllerAction();
        if ($couponcode = $controller->getRequest()->getParam('couponcode')) {
			Mage::getSingleton("checkout/session")->setDiscountLinkCouponCode($couponcode);
			// If quote is already created, add coupon code immediately
            Mage::getSingleton('checkout/cart')->getQuote()
				->setCouponCode($couponcode)
				->collectTotals()
				->save();
		}
    }
    
    /**
     * Apply coupon code from session to quote (called after add to cart and quantity change)
     *
     * @param Varien_Event_Observer $observer
     * @return Crius_DiscountLink_Model_Observer
     **/
	public function applyCoupon($observer)
	{
		if ($couponcode = Mage::getSingleton('checkout/session')->getDiscountLinkCouponCode()) {
			Mage::getSingleton('checkout/cart')->getQuote()
				->setCouponCode($couponcode)
				->collectTotals()
				->save();
		}
		return $this;
	}
	
	/**
     * When manually cancelling a coupon code from the shopping cart, also remove it from session
     *
     * @param Varien_Event_Observer $observer
     * @return Crius_DiscountLink_Model_Observer
     **/
	public function removeCoupon($observer)
	{
	    $controller = $observer->getEvent()->getControllerAction();
	    if ($controller->getRequest()->getParam('remove') == 1) {
            Mage::getSingleton("checkout/session")->setDiscountLinkCouponCode(null);
        }
	}
	
	/**
     * Add coupon URL field to shopping cart price rule editor
     * Available in Magento 1.4.1+
     *
     * @param Varien_Event_Observer $observer
     * @return Crius_DiscountLink_Model_Observer
     **/
	public function addSalesRuleUrlField($observer)
	{
	    $model = Mage::registry('current_promo_quote_rule');
	    $form = $observer->getEvent()->getForm();
	    $fieldset = $form->getElements()->offsetGet(0);
	    
	    $couponUrlField = $fieldset->addField('coupon_redirect_url', 'text', array(
            'name' => 'coupon_redirect_url',
            'label' => Mage::helper('discountlink')->__('Coupon Redirect URL'),
            'required' => false,
            'note' => Mage::helper('discountlink')->__('Type my-landing-page or my-category.html to redirect yourstore.com/couponcode to a landing page'),
            'value' => $model->getCouponRedirectUrl()
        ), 'uses_per_coupon');
	}
	
	/**
     * In shopping cart price rule editor, make the coupon URL field dependent on the coupon type field
     * Available in Magento 1.4.1+
     *
     * @param Varien_Event_Observer $observer
     * @return Crius_DiscountLink_Model_Observer
     **/
	public function addSalesRuleUrlFieldDependency($observer)
	{
	    // Look for dependency block under promo_quote_edit_tab_main
	    $block = $observer->getEvent()->getBlock();
	    if ($block->getNameInLayout() == 'promo_quote_edit_tab_main.child0') {
	        $mainBlock = $block->getParentBlock();
            $form = $mainBlock->getForm();
            $fieldset = $form->getElements()->offsetGet(0);
            
            // Add dependency: Display coupon URL field when coupon type = specific coupon is selected
            $couponUrlField = $fieldset->getElements()->searchById('coupon_redirect_url');
            $couponTypeFiled = $fieldset->getElements()->searchById('coupon_type');
	        $block->addFieldMap($couponUrlField->getHtmlId(), $couponUrlField->getName())
    	        ->addFieldDependence(
                    $couponUrlField->getName(),
                    $couponTypeFiled->getName(),
                    Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC);
	    }
	}
}