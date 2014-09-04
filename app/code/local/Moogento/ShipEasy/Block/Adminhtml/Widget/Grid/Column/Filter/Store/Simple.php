<?php

class Moogento_ShipEasy_Block_Adminhtml_Widget_Grid_Column_Filter_Store_Simple
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Store
{
    public function getHtml()
    {
        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */
        $websiteCollection = $storeModel->getWebsiteCollection();

        $allShow = $this->getColumn()->getStoreAll();

        $html  = '<select name="' . $this->_getHtmlName() . '" ' . $this->getColumn()->getValidateClass() . '>';
        $value = $this->getColumn()->getValue();
        if ($allShow) {
            $html .= '<option value="0"' . ($value == 0 ? ' selected="selected"' : '') . '>' . Mage::helper('adminhtml')->__('All Websites') . '</option>';
        } else {
            $html .= '<option value=""' . (!$value ? ' selected="selected"' : '') . '></option>';
        }

        foreach ($websiteCollection as $website) {
            $value = $this->getValue();
            $html .= '<option value="' . $website->getId() . '"' . ($value == $website->getId() ? ' selected="selected"' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;' . $website->getName() . '</option>';
        }
//        if ($this->getColumn()->getDisplayDeleted()) {
//            $selected = ($this->getValue() == '_deleted_') ? ' selected' : '';
//            $html.= '<option value="_deleted_"'.$selected.'>'.$this->__('[ deleted ]').'</option>';
//        }
        $html .= '</select>';
        return $html;
    }    
}