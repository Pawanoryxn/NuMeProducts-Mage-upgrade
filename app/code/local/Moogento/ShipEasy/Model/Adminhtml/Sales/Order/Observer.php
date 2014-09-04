<?php

class Moogento_ShipEasy_Model_Adminhtml_Sales_Order_Observer
{
    protected $_order = null;

    public function beforeSaveAttribute($observer)
    {
        $this->_order = $observer->getEvent()->getObject();
        return $this;
    }

    public function beforeGridUpdate($observer)
    {
        if ($this->_order && !in_array($this->_order->getId(), $observer->getEvent()->getProxy()->getIds())) {
            $this->_order = null;
        }

        return $this;
    }

    public function initGridColumn($observer)
    {
        $resource = $observer->getEvent()->getResource();
        if (!$this->_order) {
            return $this;
        }
        
        if ($this->_order->getIsVirtual()) {
            $resource->addVirtualGridColumn(
                'customer_name',
                'sales/order_address',
                array('billing_address_id' => 'entity_id'),
                'CONCAT(IFNULL({{table}}.firstname, ""), " ", IFNULL({{table}}.lastname, ""))'
            );
            $resource->addVirtualGridColumn(
                'country',
                'sales/order_address',
                array('billing_address_id' => 'entity_id'),
                '{{table}}.country_id'
            );
        } else {
            $resource->addVirtualGridColumn(
                'customer_name',
                'sales/order_address',
                array('shipping_address_id' => 'entity_id'),
                'CONCAT(IFNULL({{table}}.firstname, ""), " ", IFNULL({{table}}.lastname, ""))'
            );
            $resource->addVirtualGridColumn(
                'country',
                'sales/order_address',
                array('shipping_address_id' => 'entity_id'),
                '{{table}}.country_id'
            );
        }
    }
}