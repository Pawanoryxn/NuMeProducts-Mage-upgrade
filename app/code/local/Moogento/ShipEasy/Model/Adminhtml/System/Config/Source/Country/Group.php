<?php

class Moogento_ShipEasy_Model_Adminhtml_System_Config_Source_Country_Group
{
    public function getCountryGroups()
    {
        $result = array(
            ' ' => 'Unassigned'
        );

        $countryGroups = Mage::getStoreConfig('moogento_shipeasy/country_groups');
        $processedArray = array();
        foreach($countryGroups as $path => $groupInfo) {
            list($varName, $groupId) = explode('_', $path);
            $processedArray[$groupId][$varName] = $groupInfo;
        }
        $countryGroups = $processedArray;
        foreach($countryGroups as $groupInfo) {
            if (empty($groupInfo['countries'])) {
                continue;
            }
            $result[$groupInfo['label']] = $groupInfo['label'];
        }

        return $result;
    }
}