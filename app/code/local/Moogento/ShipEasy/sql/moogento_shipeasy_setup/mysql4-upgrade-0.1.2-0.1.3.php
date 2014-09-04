<?php

$installer = $this;
$this->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order'),
    'base_additional_charges',
    'decimal(12,4) NULL'
);

$this->endSetup();