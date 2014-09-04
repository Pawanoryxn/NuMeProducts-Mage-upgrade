<?php

class Moogento_ShipEasy_Model_Dataflow_Profile extends Mage_Dataflow_Model_Profile
{

    public function run()
    {
        /**
         * Prepare xml convert profile actions data
         */
        $xml = '<convert version="1.0"><profile name="default">'.$this->getActionsXml().'</profile></convert>';
        $profile = Mage::getModel('core/convert')
            ->importXml($xml)
            ->getProfile('default');
        /* @var $profile Mage_Dataflow_Model_Convert_Profile */

        try {
            $batch = Mage::getSingleton('dataflow/batch')
                ->setProfileId(0)
                ->setStoreId($this->getStoreId())
                ->save();
            $this->setBatchId($batch->getId());

            $profile->setDataflowProfile($this->getData());
            $profile->run();
        }
        catch (Exception $e) {
            echo $e;
        }

        $this->setExceptions($profile->getExceptions());
        return $this;
    }
}