<?php
/**
 * Created by JetBrains PhpStorm.
 * User: allenx
 * Date: 27/03/13
 * Time: 2:41 PM
 * To change this template use File | Settings | File Templates.
 */
class Demac_Atfeed_Block_Checkoutsuccess extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        return Mage::helper('Demac_Affiliatetraction')->getAFLinkByOrderId($orderId);
    }
}