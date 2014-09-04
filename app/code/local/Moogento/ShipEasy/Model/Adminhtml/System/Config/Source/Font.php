<?php

class Moogento_ShipEasy_Model_Adminhtml_System_Config_Source_Font
{
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label'=>Mage::helper('moogento_shipeasy')->__('Default')),
            array('value' => 'trebuchet MS', 'label'=>Mage::helper('moogento_shipeasy')->__('Trebuchet MS')),
            array('value' => 'Arial', 'label'=>Mage::helper('moogento_shipeasy')->__('Arial')),
            array('value' => 'Helvetica', 'label'=>Mage::helper('moogento_shipeasy')->__('Helvetica')),
            array('value' => 'Times New Roman', 'label'=>Mage::helper('moogento_shipeasy')->__('Times')),
            array('value' => 'Georgia', 'label'=>Mage::helper('moogento_shipeasy')->__('Georgia')),
            array('value' => 'Tahoma', 'label'=>Mage::helper('moogento_shipeasy')->__('Tahoma')),
        );
    }
}