<?php
$this->startSetup();


$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'base_subtotal',
    "decimal(12,4) default NULL"
);

$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'base_subtotal',
    'base_subtotal'
);

$select = $this->getConnection()->select();

$select->join(
    array('order_table'=>$this->getTable('sales/order')),
    'order_table.entity_id = order_grid.entity_id',
    array('base_subtotal' => 'base_subtotal')
);

$this->getConnection()->query(
    $select->crossUpdateFromSelect(
        array('order_grid' => $this->getTable('sales/order_grid'))
    )
);

$this->endSetup();