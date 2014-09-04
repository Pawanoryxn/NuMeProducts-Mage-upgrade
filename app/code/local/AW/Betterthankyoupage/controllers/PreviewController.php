<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Betterthankyoupage
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


/**
 * 
 */
class AW_Betterthankyoupage_PreviewController extends Mage_Core_Controller_Front_Action {
    
    /**
     * 
     */
    public function indexAction() {
        $this->_redirect('*/*/onepage');
    }
    
    
    
    /**
     * 
     */
    public function onepageAction() {
        if ( !$this->__isLoggedInAdminArea() or !$this->__isAllowedToPreviewInACL() ) {
            $this->_forward('noRoute');
            return false;
        }
        
        $this->__simulateLayout('checkout_onepage_success');
        $__fakeLastOrderID = intval($this->getRequest()->getParam('order_id'));
        $__realLastOrderID = Mage::getSingleton('checkout/session')->getLastOrderId();
        $__fakeLastOrderIDs = array();
        $__realLastOrderIDs = Mage::getSingleton('core/session')->getOrderIds();
        Mage::getSingleton('checkout/session')->setLastOrderId($__fakeLastOrderID);
        Mage::getSingleton('core/session')->setOrderIds($__fakeLastOrderIDs);
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->setLastOrderId($__realLastOrderID);
        Mage::getSingleton('core/session')->setOrderIds($__realLastOrderIDs);
        
        return $this;
    }
    
    
    
    /**
     * 
     */
    public function multishippingAction() {
        if ( !$this->__isLoggedInAdminArea() or !$this->__isAllowedToPreviewInACL() ) {
            $this->_forward('noRoute');
            return false;
        }
        
        $this->__simulateLayout('checkout_multishipping_success');
        $__fakeLastOrderID = 0;
        $__realLastOrderID = Mage::getSingleton('checkout/session')->getLastOrderId();
        $__fakeLastOrderIDs = explode(',', $this->getRequest()->getParam('order_ids'));
        $__realLastOrderIDs = Mage::getSingleton('core/session')->getOrderIds();
        Mage::getSingleton('checkout/session')->setLastOrderId($__fakeLastOrderID);
        Mage::getSingleton('core/session')->setOrderIds($__fakeLastOrderIDs);
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->setLastOrderId($__realLastOrderID);
        Mage::getSingleton('core/session')->setOrderIds($__realLastOrderIDs);
        
        return $this;
    }
    
    
    
    /**
     * 
     */
    protected function __simulateLayout($handle) {
        $this->getLayout()->getUpdate()->addHandle('default');
        $this->getLayout()->getUpdate()->addHandle($handle);
        if ( Mage::getSingleton('customer/session')->isLoggedIn() ) {
            $this->getLayout()->getUpdate()->addHandle('customer_logged_in');
        }
        else {
            $this->getLayout()->getUpdate()->addHandle('customer_logged_out');
        }
        $this->getLayout()->getUpdate()->load();
        $this->getLayout()->generateXml();
        $this->getLayout()->generateBlocks();
        
        return $this;
    }
    
    
    
    /**
     * 
     */
    protected function __isLoggedInAdminArea() {
        $__url = Mage::helper('adminhtml')->getUrl('betterthankyoupage');
        $__H = curl_init($__url);
        $__cookies = '';
        foreach ( $_COOKIE as $__key => $__value ) $__cookies .= $__key . '=' . $__value . ';';
        curl_setopt($__H, CURLOPT_COOKIE, $__cookies);
        curl_setopt($__H, CURLOPT_RETURNTRANSFER, 1);
        $__R = curl_exec($__H);
        
        return ( $__R === '{"loggedIn": 1}' );
    }
    
    
    
    /**
     * @TODO
     */
    protected function __isAllowedToPreviewInACL() {
        return true;
    }
}