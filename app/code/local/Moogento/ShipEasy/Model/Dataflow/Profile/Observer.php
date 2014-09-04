<?php

class Moogento_ShipEasy_Model_Dataflow_Profile_Observer
{
    public function addFilterToProfileCollection($observer)
    {
        $collection = $observer->getCollection();
        if ($collection instanceof Mage_Dataflow_Model_Mysql4_Profile_Collection) {
            $collection->addFieldToFilter(
                'profile_id',
                array(
                    'neq' => 0
                )
            );
        }
    }
}