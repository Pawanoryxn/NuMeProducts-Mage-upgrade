<?php

class Moogento_ShipEasy_Model_Shipping_Carrier_Thai_Post_Manual extends Mage_Shipping_Model_Carrier_Flatrate
{
    protected $_code = 'thai_post_manual';
    protected $_isFixed = true;

    public function isTrackingAvailable()
    {
        return true;
    }
}