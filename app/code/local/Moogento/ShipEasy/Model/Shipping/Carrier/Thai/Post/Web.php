<?php

class Moogento_ShipEasy_Model_Shipping_Carrier_Thai_Post_Web extends Mage_Shipping_Model_Carrier_Flatrate
{
    protected $_code = 'thai_post_web';
    protected $_isFixed = true;

    public function isTrackingAvailable()
    {
        return true;
    }
}