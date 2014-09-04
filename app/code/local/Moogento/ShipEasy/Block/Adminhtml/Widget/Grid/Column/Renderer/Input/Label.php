<?php

class Moogento_ShipEasy_Block_Adminhtml_Widget_Grid_Column_Renderer_Input_Label
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function renderHeader()
    {
        $out = '';
        $out = $this->getColumn()->getHeader();
        return $out;
    }


    public function render(Varien_Object $row)
    {
        $html = '';
        if ($row->getData($this->getColumn()->getIndex())) {
            $link = $row->getData($this->getColumn()->getIndex());
            try {
                $links = unserialize($link);
                if ($links) {
                  foreach($links as $link) {
                      $html .= '<a target="_blank" href="'.$link['link'].'">'.$link['title'].'</a>';
                      $html .= '<br />';
                  }
                } else {
                  $html .= $link;
                }
                return $html;
            } catch (Exception $e) {
                asdf();
                return $link;
            }
        } else {
            return parent::render($row);
        }
    }
}