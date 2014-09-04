<?php

$installer = $this;
$this->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_shipping_cost', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'base_profit', 'decimal(12,4) NULL');


$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'base_shipping_cost',
    "decimal(12,4) default NULL"
);

$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'base_shipping_cost',
    'base_shipping_cost'
);


$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'base_profit',
    "decimal(12,4) default NULL"
);

$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'base_profit',
    'base_profit'
);

$this->endSetup();