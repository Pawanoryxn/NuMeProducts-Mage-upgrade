<?php


class Moogento_ShipEasy_Block_Adminhtml_Widget_Grid_Column_Renderer_Country_Group
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        $showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
        if (!empty($options) && is_array($options)) {
            $value = $row->getData($this->getColumn()->getIndex());
            if (is_array($value)) {
                $res = array();
                foreach ($value as $item) {
                    if (isset($options[$item])) {
                        $res[] = $options[$item];
                    }
                    elseif ($showMissingOptionValues) {
                        $res[] = $item;
                    }
                }
                return implode(', ', $res);
            }
            elseif (isset($options[$value])) {
                if ($value == ' ') {
                    return ' ';
                }
                return $options[$value];
            }
            return '';
        }
    }    
}