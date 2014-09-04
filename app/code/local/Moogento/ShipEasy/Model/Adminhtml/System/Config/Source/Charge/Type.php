<?php

class Moogento_ShipEasy_Model_Adminhtml_System_Config_Source_Charge_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('moogento_shipeasy')->__('Fixed')),
            array('value' => 1, 'label'=>Mage::helper('moogento_shipeasy')->__('Percent')),
        );
    }

}