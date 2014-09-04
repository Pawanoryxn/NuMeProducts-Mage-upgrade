<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amacgregor
 * Date: 09/05/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */

class Demac_Atfeed_Model_Resource_Feed extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init('atfeed/feed', 'id');
    }


    /**
     * Get empty select object
     *
     * @return Varien_Db_Select
     */
    public function createSelect()
    {
        return $this->_getReadAdapter()->select();
    }
}