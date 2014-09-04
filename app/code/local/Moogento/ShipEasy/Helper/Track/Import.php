<?php

class Moogento_ShipEasy_Helper_Track_Import extends Mage_Core_Helper_Abstract
{
    public function getImportProfile($path, $fileName)
    {
        $profile = Mage::getModel('moogento_shipeasy/dataflow_profile')->load(0);

        if ((int)$profile->getProfileId() !== 0 ){
            die('error');
        }

        $actionsXml = $profile->getActionsXml();
        $actionsXml = str_replace('{{path}}', $path, $actionsXml);
        $actionsXml = str_replace('{{filename}}', $fileName, $actionsXml);
        $profile->setActionsXml($actionsXml);

        $guiData = $profile->getGuiData();
        $guiData['file']['filename'] = $fileName;
        $guiData['file']['path'] = $path;
        $profile->setGuiData($guiData);

        return $profile;
    }
}