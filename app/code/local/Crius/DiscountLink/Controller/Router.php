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
 * DiscountLink Controller Router
 */
class Crius_DiscountLink_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('discountlink', $this);
    }

    /**
     * Validate and match discount code and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        $couponcode = trim($request->getPathInfo(), '/');
        
        // If the request string matches a coupon code
        if ($couponcode && Mage::helper('discountlink')->validateCouponCode($couponcode)) {
            // Go to /discountlink/index/setCoupon?couponcode=X
            $request->setModuleName('discountlink')
                ->setControllerName('index')
                ->setActionName('setCoupon')
                ->setParam('couponcode', $couponcode);
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $couponcode
            );
            return true;
        } else {
            return false;
        }
    }
}
