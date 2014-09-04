<?php

class Moogento_ShipEasy_Model_Mysql4_Sales_Order extends Mage_Sales_Model_Mysql4_Order
{

    public function getOrderColumnValue($order, $column)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(
            $this->getTable('sales/order'),
            'customer_email'
        );
        $select->where(
            'entity_id = ' . $order->getId()
        );

        return $this->_getReadAdapter()->fetchOne($select);
    }

    public function updateGridRow($order, $column, $data)
    {
        $this->_getWriteAdapter()->update(
            $this->getTable('sales/order_grid'),
            array($column => $data),
            $this->_getWriteAdapter()->quoteInto('entity_id = ?',$order->getId())
        );
    }

    protected function _quoteCountryId($id)
    {
        return '\'' . trim($id) . '\'';
    }

    protected function _getCountryGroupSql()
    {
        $countryGroups = Mage::getStoreConfig('moogento_shipeasy/country_groups');

        $processedArray = array();

        foreach($countryGroups as $path => $groupInfo) {
            list($varName, $groupId) = explode('_', $path);
            $processedArray[$groupId][$varName] = $groupInfo;
        }

        $countryGroups = $processedArray;


        $sql = '';

        $count = 0;
        foreach($countryGroups as $groupInfo) {
            $countries = trim($groupInfo['countries']);
            if (empty($countries)) {
                continue;
            }
            $count++;

            $countries = explode(',', $countries);
            $countries = array_map(array($this, '_quoteCountryId'), $countries);
            $countries = implode(',', $countries);

            $sql .= 'IF (`address`.`country_id` IN ('.$countries.'), "'.$groupInfo['label'].'", ';
        }

        $sql .= '" "';

        for($i=0; $i<$count; $i++) {
            $sql .= ')';
        }

        return $sql;
    }

    protected function _getStatusFilter()
    {
        $statuses = Mage::getStoreConfig('moogento_shipeasy/weight/statuses');
        if (!$statuses) {
            return false;
        }
        $statuses = explode(',', $statuses);
        return $statuses;
    }

    public function getWeightPerRegionGroup()
    {
         
        $select = $this->_getReadAdapter()->select();
        $select->from(
            array('main_table' => $this->getTable('sales/order')),
            array()
        );
        $select->join(
            array('address' => $this->getTable('sales/order_address')),
            'main_table.shipping_address_id = address.entity_id',
            array()
        );
        $select->columns(
            array('country_group' => new Zend_Db_Expr($this->_getCountryGroupSql()))
        );
        $select->columns(
            array('weight' => new Zend_Db_Expr('SUM(main_table.weight)'))
        );
        $select->columns(
            array('order_count' => new Zend_Db_Expr('COUNT(main_table.entity_id)'))
        );

        if ($filterStatuses = $this->_getStatusFilter()) {
            $select->where(
                $this->_getReadAdapter()->quoteInto(
                    'main_table.status IN (?)', $filterStatuses
                )
            );
        }

        $select->group('country_group');
        return $this->_getReadAdapter()->fetchAssoc($select);
    }
}