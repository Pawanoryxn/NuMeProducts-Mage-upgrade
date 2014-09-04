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
class AW_Betterthankyoupage_Block_SectionOrderinformation extends Mage_Core_Block_Template {

    /**
     *
     */
    private $__singleOrderModel       = null;
    private $__mulptipleOrderModels   = null;
    private $__recurringProdileModels = null;


    /**
     *
     */
    public function isSingleOrder() {
        $__lastMultipleshippingOrderIDs = $this->__getLastMultipleOrderIdsFromSession();
        if ( is_array($__lastMultipleshippingOrderIDs) ) {
            $__lastMultipleshippingOrderIDs = array_values($__lastMultipleshippingOrderIDs);
            if ( count($__lastMultipleshippingOrderIDs) > 0 ) {
                if ( $__lastMultipleshippingOrderIDs[0] > $this->getOrderNumber() ) {
                    return false;
                }
            }
        }

        return true;
    }



    /**
     *
     */
    public function getOrder() {
        if ( is_null($this->__singleOrderModel) ) {
            $this->__singleOrderModel = Mage::getModel('sales/order')->load(
                $this->__getLastSingleOrderIdFromSession()
            );
            if ( ! $this->__singleOrderModel->getId() ) {
                $this->__singleOrderModel->loadByIncrementId(
                    $this->__getLastSingleOrderIdFromSession()
                );
                if ( ! $this->__singleOrderModel->getId() ) $this->__singleOrderModel = false;
            }
        }

        return $this->__singleOrderModel;
    }



    /**
     *
     */
    public function getOrders() {
        if ( is_null($this->__mulptipleOrderModels) ) {
            $this->__mulptipleOrderModels = array();

            $__lastMultipleshippingOrderIDs = $this->__getLastMultipleOrderIdsFromSession();
            if ( ! is_array($__lastMultipleshippingOrderIDs) ) $__lastMultipleshippingOrderIDs = array();

            foreach ( $__lastMultipleshippingOrderIDs as $__orderId ) {
                $__orderModel = Mage::getModel('sales/order')->loadByIncrementId( $__orderId );
                if ( ! $__orderModel->getId() ) $__orderModel->load( $__orderId );
                if ( $__orderModel->getId() ) array_push($this->__mulptipleOrderModels, $__orderModel);
            }
        }

        return $this->__mulptipleOrderModels;
    }



    /**
     *
     */
    public function getOrderId() {
        $__orderId = null;
        if ( $this->getOrder() ) {
            $__orderId = $this->getOrder()->getId();
        }

        return $__orderId;
    }



    /**
     *
     */
    public function getOrderIds() {
        $__orderIds = array();
        foreach ( $this->getOrders() as $__order ) {
            array_push($__orderIds, $__order->getId());
        }

        return $__orderIds;
    }



    /**
     *
     */
    public function getOrderNumber() {
        $__orderNumber = null;

        if ( $this->getOrder() ) {
            $__orderNumber = $this->getOrder()->getIncrementId();
        }

        return $__orderNumber;
    }



    /**
     *
     */
    public function getOrderNumbers() {
        return $this->__getLastMultipleOrderIdsFromSession();
    }



    /**
     *
     */
    public function canViewOrder($order) {
        $__isOrderVisible = ( $order ) and ( ! in_array(
            $order->getState(),
            Mage::getSingleton('sales/order_config')->getInvisibleOnFrontStates()
        ) );
        $__customerIsLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();

        return ($__isOrderVisible and $__customerIsLoggedIn);
    }


    public function getRecurringProfiles()
    {
        if ( is_null($this->__recurringProdileModels) ) {



            $profileIds = $this->__getLastRecurringProfileIdsFromSession();
            if ($profileIds && is_array($profileIds)) {
                $isSarpProfiles = false;
                if (Mage::helper('betterthankyoupage')->isSarp2Installed()) {
                    $collection = Mage::getModel('aw_sarp2/profile')->getCollection()
                        ->addFieldToFilter('entity_id', array('in' => $profileIds))
                    ;
                    if ($collection->getSize() == count($profileIds)) {
                        $isSarpProfiles = true;
                        foreach ($collection as $profile) {
                            $profile->load($profile->getId());
                            $profile->setData('schedule_description', $profile->getData('details/description'));
                        }
                    }
                }
                if (!$isSarpProfiles) {
                    $collection = Mage::getModel('sales/recurring_profile')
                        ->getCollection()
                        ->addFieldToFilter('profile_id', array('in' => $profileIds))
                    ;
                }
                $profiles = array();
                foreach ($collection as $profile) {
                    $profiles[] = $profile;
                }
                if ($profiles) {
                    $this->__recurringProdileModels = $profiles;
                }
            }
        }
        return $this->__recurringProdileModels;
    }

    /**
     *
     */
    public function getCanViewProfiles()
    {
        if (count($this->getRecurringProfiles()) > 0 && Mage::getSingleton('customer/session')->isLoggedIn()) {
           return true;
        }
        return false;
    }


    /**
     *
     */
    public function canViewInvoice($order) {
        $__orderHasInvoices = ( $order ) and ( $order->hasInvoices() );
        $__customerIsLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();

        return ($__orderHasInvoices and $__customerIsLoggedIn);
    }



    /**
     *
     */
    public function getViewOrderUrl($orderId) {
        return $this->getUrl('sales/order/view/', array('order_id' => $orderId));
    }



    /**
     *
     */
    public function getViewInvoiceUrl($orderId) {
        return Mage::getUrl('sales/order/invoice', array('order_id' => $orderId));
    }

    /**
     * Getter for recurring profile view page
     *
     * @param Varien_Object $profile
     * @return string
     */
    public function getProfileUrl(Varien_Object $profile)
    {
        if ($profile instanceof AW_Sarp2_Model_Profile) {
            return $this->getUrl('aw_recurring/customer/view', array('id' => $profile->getId()));
        }
        return $this->getUrl('sales/recurring_profile/view', array('profile' => $profile->getId()));
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
    protected function __getLastRecurringProfileIdsFromSession() {
        return Mage::getSingleton('checkout/session')->getLastRecurringProfileIds();
    }


    /**
     *
     */
    protected function __getLastSingleOrderIdFromSession() {
        return Mage::getSingleton('checkout/session')->getLastOrderId();
    }



    /**
     *
     */
    protected function __getLastMultipleOrderIdsFromSession() {
        return Mage::getSingleton('core/session')->getOrderIds();
    }



    /**
     *
     */
    public function shouldDisplaySection() {
        return ( Mage::getStoreConfig('betterthankyoupage/order_information_section/display_section') ? true : false );
    }



    /**
     *
     */
    public function getSortOrder() {
        return intval( Mage::getStoreConfig('betterthankyoupage/order_information_section/section_sort_order') );
    }



    /**
     *
     */
    public function shouldDisplayOrderNumber() {
        return ( Mage::getStoreConfig('betterthankyoupage/order_information_section/display_order_number') ? true : false );
    }



    /**
     *
     */
    public function shouldDisplayInvoiceButton() {
        return ( Mage::getStoreConfig('betterthankyoupage/order_information_section/display_invoice_button') ? true : false );
    }



    /**
     *
     */
    public function getTextMessage() {
        return strip_tags(Mage::getStoreConfig('betterthankyoupage/order_information_section/text_message'), '<b><i><a><p><br>');
    }



    /**
     *
     */
    protected function _beforeToHtml() {
        return $this;
    }
}