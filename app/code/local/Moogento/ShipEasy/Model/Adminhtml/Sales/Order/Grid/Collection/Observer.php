<?php

class Moogento_ShipEasy_Model_Adminhtml_Sales_Order_Grid_Collection_Observer
{

    public function loadBefore($observer)
    {
        $collection = $observer->getData('collection');
        if (!($collection instanceof Mage_Sales_Model_Mysql4_Order_Grid_Collection)) {
            return $this;
        }
        $select = $collection->getSelect();
        $sql = Mage::helper('moogento_shipeasy/grid')->getCountryGroupSql();
                       
        $select->columns(
            array('country_region' => new Zend_Db_Expr($sql))
        );

    }
}