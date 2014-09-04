<?php

class Moogento_ShipEasy_Model_Adminhtml_System_Config_Source_Grid_Store
{
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Simplified [only Website]')),
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Standard')),
        );
    }
}