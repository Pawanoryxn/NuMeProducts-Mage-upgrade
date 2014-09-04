<?php

$installer = $this;
$this->startSetup();


$trackingNoColumnName = Mage::helper('moogento_shipeasy/track')->getTrackNoColumnName();

$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'tracking_number',
    "varchar(255) default NULL"
);

$select = $this->getConnection()->select();

$select->join(
    array('tracking_table'=>$this->getTable('sales/shipment_track')),
    'tracking_table.order_id = order_grid.entity_id',
    array('tracking_number' => $trackingNoColumnName)
);

$sql = $select->crossUpdateFromSelect(
    array('order_grid' => $this->getTable('sales/order_grid'))
);

$this->getConnection()->query($sql);

$this->endSetup();