<?php
$this->startSetup();
$installer = $this;

$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'product_names',
    "text"
);

$_allowedProductTypes = array('bundle', 'simple', 'virtual', 'downloadable');

$select = $this->getConnection()->select();
$select->from(
    $this->getTable('sales/order_item'),
    array('order_id', 'name')
);
$select->where(
    $this->getConnection()->quoteInto(
        'product_type IN (?)',
        $_allowedProductTypes
    )
);

$result = $this->getConnection()->fetchAll($select);
$orderNames = array();

foreach($result as $orderItem) {
    $orderId = $orderItem['order_id'];
    if (isset($orderNames[$orderId])) {
        $orderNames[$orderId] .= ',' . $orderItem['name'];
    } else {
        $orderNames[$orderId] = $orderItem['name'];
    }
}

$counter = 0;
$sql = '';

foreach($orderNames as $orderId => $productNames) {
    $productNames = $this->getConnection()->quote($productNames);
    $sql .= "UPDATE `{$this->getTable('sales/order_grid')}` SET `product_names` = {$productNames} WHERE entity_id = {$orderId};";
    $counter++;
    if ($counter%1000 == 0) {
        $this->getConnection()->beginTransaction();
        $this->run($sql);
        $this->getConnection()->commit();
        $sql = '';
    }
}

if (!empty($sql)) {
    $this->getConnection()->beginTransaction();
    $this->run($sql);
    $this->getConnection()->commit();
}


$this->endSetup();