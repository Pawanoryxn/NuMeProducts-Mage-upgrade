<?php

class Moogento_ShipEasy_Block_Adminhtml_System_Config_Form_Element_Select
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr><td colspan="2" style="padding-top: 20px;"><strong>'.$element->getLabel().'</strong></td></tr>';
        $element->setLabel('Show');
        return $html . parent::render($element);
    }
}