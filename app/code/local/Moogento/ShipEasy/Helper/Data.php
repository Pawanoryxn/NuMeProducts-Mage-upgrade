<?php

class Moogento_ShipEasy_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getDefaultTrackingLink($trackingNo)
    {
        $baseLink = Mage::getStoreConfig('moogento_shipeasy/grid/tracking_number_base_link');
        return $baseLink . $trackingNo;
    }

    public function getOrderFilterStyle()
    {
        $style = '';
        if (Mage::getStoreConfig('moogento_shipeasy/fonts/font')) {
            $style .= 'font-family:'.Mage::getStoreConfig('moogento_shipeasy/fonts/font').';';
        }

        if (Mage::getStoreConfig('moogento_shipeasy/fonts/size')) {
            $style .= 'font-size:'.Mage::getStoreConfig('moogento_shipeasy/fonts/size').'px;';
        }
        return $style;
    }

    public function getOrderRowStyle($order)
    {
        $style = '';
        $color = Mage::getStoreConfig('moogento_shipeasy/colors/'.$order->getStatus());
        if ($color) {
            $style='background-color: ' . $color . ';';
        }
        
        if (Mage::getStoreConfig('moogento_shipeasy/fonts/font')) {
            $style .= 'font-family:'.Mage::getStoreConfig('moogento_shipeasy/fonts/font').';';
        }

        if (Mage::getStoreConfig('moogento_shipeasy/fonts/size')) {
            $style .= 'font-size:'.Mage::getStoreConfig('moogento_shipeasy/fonts/size').'px;';
        }
        return $style;
    }
}