<?php


class Moogento_ShipEasy_Block_Adminhtml_Widget_Grid_Column_Renderer_Country
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        return $this->getLayout()->createBlock('moogento_shipeasy/adminhtml_directory_country_flag')
            ->setCountryId($row->getCountry())
            ->toHtml();
    }
    
}