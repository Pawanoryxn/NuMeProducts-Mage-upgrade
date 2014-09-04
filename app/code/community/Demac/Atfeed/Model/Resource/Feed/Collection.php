<?php

class Demac_Atfeed_Model_Resource_Feed_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init('atfeed/feed');
    }

}