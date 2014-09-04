<?php

class Moogento_ShipEasy_Model_Adminhtml_System_Config_Source_Grid_Yesno
{
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Yes')),
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('No')),
        );
    }

}