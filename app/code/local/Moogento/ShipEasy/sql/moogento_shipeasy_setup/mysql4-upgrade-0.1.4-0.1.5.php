<?php
$installer = $this;
$this->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('sales/order_grid')}`
    MODIFY COLUMN `tracking_number` text;
");

$this->endSetup();
