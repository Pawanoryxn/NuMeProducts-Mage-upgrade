<?php

class Moogento_ShipEasy_Block_Adminhtml_Widget_Grid_Column_Renderer_Store_Simple
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store
{
    public function render(Varien_Object $row)
    {
        $out = '';
        $skipAllStoresLabel = $this->_getShowAllStoresLabelFlag();
        $skipEmptyStoresLabel = $this->_getShowEmptyStoresLabelFlag();
        $origStores = $row->getData($this->getColumn()->getIndex());

        if (is_null($origStores) && $row->getStoreName()) {
            $scopes = array();
            foreach (explode("\n", $row->getStoreName()) as $k => $label) {
                $scopes[] = str_repeat('&nbsp;', $k * 3) . $label;
            }
            $out .= implode('<br/>', $scopes) . $this->__(' [deleted]');
            return $out;
        }

        if (empty($origStores)&& !$skipEmptyStoresLabel) {
            return '';
        }
        if (!is_array($origStores)) {
            $origStores = array($origStores);
        }

        if (empty($origStores)) {
            return '';
        }
        elseif (in_array(0, $origStores) && count($origStores) == 1 && !$skipAllStoresLabel) {
            return Mage::helper('adminhtml')->__('All Store Views');
        }

        $data = $this->_getStoreModel()->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $out .= $website['label'] . '<br/>';
        }
        return $out;
    }


    public function renderExport(Varien_Object $row)
    {
        $out = '';
        $skipAllStoresLabel = $this->_getShowAllStoresLabelFlag();
        $origStores = $row->getData($this->getColumn()->getIndex());

        if (is_null($origStores) && $row->getStoreName()) {
            $scopes = array();
            foreach (explode("\n", $row->getStoreName()) as $k => $label) {
                $scopes[] = str_repeat(' ', $k * 3) . $label;
            }
            $out .= implode("\r\n", $scopes) . $this->__(' [deleted]');
            return $out;
        }

        if (!is_array($origStores)) {
            $origStores = array($origStores);
        }

        if (in_array(0, $origStores) && !$skipAllStoresLabel) {
            return Mage::helper('adminhtml')->__('All Store Views');
        }

        $data = $this->_getStoreModel()->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $out .= $website['label'] . "\r\n";
        }

        return $out;
    }
}