<?php

class Moogento_ShipEasy_Block_Adminhtml_Widget_Grid_Column_Renderer_Input_Shippingcost
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency
{
    public function render(Varien_Object $row)
    {
        if (!is_null($row->getData($this->getColumn()->getIndex()))) {
            return parent::render($row);
        } else {
            $html = '<input type="text" ';
            $html .= 'name="' . $this->getColumn()->getId() . '" ';
            $html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '"';
            $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
            return $html;
        }
    }
}