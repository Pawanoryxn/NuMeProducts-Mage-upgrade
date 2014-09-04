<?php

class Demac_Atfeed_Model_Cron
{
    public function run()
    {
        $feeds = Mage::getResourceModel('atfeed/feed_collection')
                ->addFieldToFilter('is_active', 1);


        try {
            foreach ($feeds as $feed) {
                $collection = Mage::getResourceModel('atfeed/feedattribute_collection')
                    ->addFieldToFilter('feed_id', $feed->getId())
                    ->load();

                foreach ($collection as $attribute) {
                    $attributes[] = $attribute->getData();
                }
                $generator = Mage::getModel('atfeed/generator')->initialize($feed, $attributes);

                $generator->generateTabDelimitedFile();
                $generator->uploadFeed();
                $generator->setRuleStatus(1);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }


    }
    
}