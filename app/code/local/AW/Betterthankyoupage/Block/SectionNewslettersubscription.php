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
class AW_Betterthankyoupage_Block_SectionNewslettersubscription extends Mage_Newsletter_Block_Subscribe {
    
    /**
     * 
     */
    public function getOrder() {
        return Mage::helper('betterthankyoupage/successPage')->getOrder();
    }
    
    
    
    /**
     * 
     */
    protected function _toHtml() {
        if ( Mage::getStoreConfig('betterthankyoupage/general/module_enabled') ) {
            return parent::_toHtml();
        }
        
        return null;
    }
    
    
    /**
     * 
     */
    public function shouldDisplaySection() {
        return (
            (
                ( Mage::getStoreConfig('betterthankyoupage/newsletter_subscription_section/display_section') )
                and
                ( intval(Mage::getStoreConfig('advanced/modules_disable_output/Mage_Newsletter')) == 0 )
            )
            ? true : false
        );
    }
    
    
    /**
     * 
     */
    public function getSortOrder() {
        return intval( Mage::getStoreConfig('betterthankyoupage/newsletter_subscription_section/section_sort_order') );
    }
    
    
    /**
     *
     */
    public function getCustomerEmail() {
        $__customerEmail = null;
        
        $__H = Mage::helper('betterthankyoupage/successPage');
        if ( $__H->isSingleOrder() and $__H->getOrder() ) {
            $__customerEmail = $__H->getOrder()->getCustomerEmail();
        }
        else {
            $__orders = $__H->getOrders();
            if ( is_array($__orders) and (count($__orders) > 0) and (is_object($__orders[0])) ) {
                $__customerEmail = $__orders[0]->getCustomerEmail();
            }
        }
        
        return $__customerEmail;
    }
    
    
    /**
     * 
     */
    public function isCustomerSubscribed() {
        $__isSubscribed = false;
        
        $__subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail(
            $this->getCustomerEmail()
        );
        if ( $__subscriber ) {
            $__isSubscribed = $__subscriber->isSubscribed();
        }
        
        return $__isSubscribed;
    }
}