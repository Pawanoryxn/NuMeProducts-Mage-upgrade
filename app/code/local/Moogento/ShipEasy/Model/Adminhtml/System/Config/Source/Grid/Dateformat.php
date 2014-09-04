<?php

class Moogento_ShipEasy_Model_Adminhtml_System_Config_Source_Grid_Dateformat
{
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('moogento_shipeasy')->__('Simplified [eg. 14.03.11 19:24]')),
            array('value' => 2, 'label'=>Mage::helper('moogento_shipeasy')->__('Standard [eg. Mar 14, 2011 7:24:37 PM]')),
        );
    }

}