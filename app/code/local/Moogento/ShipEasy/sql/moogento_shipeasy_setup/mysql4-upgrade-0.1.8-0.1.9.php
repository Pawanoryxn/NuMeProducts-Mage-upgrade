<?php

$this->startSetup();
$installer = $this;

$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'product_skus',
    "tinytext"
);

$_allowedProductTypes = array('bundle', 'simple', 'virtual', 'downloadable');

$select = $this->getConnection()->select();
$select->from(
    $this->getTable('sales/order_item'),
    array('order_id', 'sku')
);
$select->where(
    $this->getConnection()->quoteInto(
        'product_type IN (?)',
        $_allowedProductTypes
    )
);


$result = $this->getConnection()->fetchAll($select);
$orderSkus = array();


foreach($result as $orderItem) {
    $orderId = $orderItem['order_id'];
    if (isset($orderSkus[$orderId])) {
        $orderSkus[$orderId] .= ',' . $orderItem['sku'];
    } else {
        $orderSkus[$orderId] = $orderItem['sku'];
    }
}

$counter = 0;
$sql = '';

foreach($orderSkus as $orderId => $productSkus) {
    $productSkus = $this->getConnection()->quote($productSkus);
    $sql .= "UPDATE `{$this->getTable('sales/order_grid')}` SET `product_skus` = {$productSkus} WHERE entity_id = {$orderId};";
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