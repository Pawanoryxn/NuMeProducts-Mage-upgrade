<?php

class Moogento_ShipEasy_Block_Adminhtml_Sales_Order_After extends Mage_Adminhtml_Block_Template
{
    protected function _toHtml()
    {
        if ((bool)Mage::getStoreConfig('moogento_shipeasy/weight/enabled')) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    protected function _getMeasureUnit()
    {
        return Mage::getStoreConfig('moogento_shipeasy/weight/measure_unit');
    }

    protected function _getCountryGroups()
    {
        $countryGroups = Mage::getStoreConfig('moogento_shipeasy/country_groups');
        $processedArray = array();
        foreach($countryGroups as $path => $groupInfo) {
            list($varName, $groupId) = explode('_', $path);
            $processedArray[$groupId][$varName] = $groupInfo;
        }
        $countryGroups = $processedArray;

        $data = array();

        $weight = Mage::getResourceModel('moogento_shipeasy/sales_order')->getWeightPerRegionGroup();

        foreach($countryGroups as $groupInfo) {
            if (!trim($groupInfo['countries'])) {
                continue;
            }

            $data[$groupInfo['label']]['weight'] = isset($weight[$groupInfo['label']]) ? $weight[$groupInfo['label']]['weight'] : 0;
            $data[$groupInfo['label']]['weight'] = round($data[$groupInfo['label']]['weight'], 2);
            $data[$groupInfo['label']]['orders'] = isset($weight[$groupInfo['label']]) ? $weight[$groupInfo['label']]['order_count'] : 0;
        }

        $data['Others']['weight'] = isset($weight[' ']) ? $weight[' ']['weight'] : 0;
        $data['Others']['weight'] = round($data['Others']['weight'], 2);
        $data['Others']['orders'] = isset($weight[' ']) ? $weight[' ']['order_count'] : 0;

        return $data;
    }
}