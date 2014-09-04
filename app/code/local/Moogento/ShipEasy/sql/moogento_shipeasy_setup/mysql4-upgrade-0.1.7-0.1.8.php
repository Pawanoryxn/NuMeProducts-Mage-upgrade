<?php

$this->startSetup();
$installer = $this;

$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'country',
    "varchar(255) not null default ''"
);

$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'country',
    'country'
);

$select = $this->getConnection()->select();

$select->join(
    array('billing_address'=>$this->getTable('sales/order_address')),
    $this->getConnection()->quoteInto(
        'billing_address.parent_id = order_grid.entity_id AND billing_address.address_type = ?',
        Mage_Sales_Model_Quote_Address::TYPE_BILLING
    ),
    array()
);

$select->joinLeft(
    array('shipping_address'=>$this->getTable('sales/order_address')),
    $this->getConnection()->quoteInto(
        'shipping_address.parent_id = order_grid.entity_id AND shipping_address.address_type = ?',
        Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
    ),
    array()
);

$select->columns(array(
    "country" => new Zend_Db_Expr('
        IF (
            shipping_address.firstname IS NULL,
            billing_address.country_id,
            shipping_address.country_id
        )
    ')
));

$this->getConnection()->query(
    $select->crossUpdateFromSelect(
        array('order_grid' => $this->getTable('sales/order_grid'))
    )
);




$this->endSetup();