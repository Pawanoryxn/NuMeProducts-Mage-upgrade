<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amacgregor
 * Date: 09/05/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */

class Demac_Atfeed_Model_Resource_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init('atfeed/category', 'id');
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


    /**
     * @param \Demac_Bogo_Model_Index|\Demac_Loyalty_Model_Loyalty $object
     * @param $product_id
     * @internal param $customer_id
     * @internal param $rule_id
     * @return int
     */
    public function loadByCategoryName(Demac_Atfeed_Model_Category $object, $name, $level)
    {
        $adapter = $this->_getReadAdapter();
        $where = $adapter->quoteInto("name = ? AND ", $name).$adapter->quoteInto("category_level = ? ", $level);
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where($where);
        if ($data = $adapter->fetchRow($select)) {
            $object->setData($data);
            $this->_afterLoad($object);
        }
        return $this;
    }


}