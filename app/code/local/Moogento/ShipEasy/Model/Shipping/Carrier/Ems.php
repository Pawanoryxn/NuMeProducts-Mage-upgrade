<?php

class Moogento_ShipEasy_Model_Shipping_Carrier_Ems extends Mage_Shipping_Model_Carrier_Flatrate
{
    protected $_code = 'ems';
    protected $_isFixed = true;

    public function isTrackingAvailable()
    {
        return true;
    }
}