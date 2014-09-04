<?php

$this->startSetup();


$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'customer_name',
    "varchar(255) not null default ''"
);

$this->getConnection()->addKey(
    $this->getTable('sales/order_grid'),
    'customer_name',
    'customer_name'
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
    "customer_name" => new Zend_Db_Expr('
        IF (
            shipping_address.firstname IS NULL,
            CONCAT(IFNULL(billing_address.firstname, ""), " ", IFNULL(billing_address.lastname, "")),
            CONCAT(IFNULL(shipping_address.firstname, ""), " ", IFNULL(shipping_address.lastname, ""))
        )
    ')
));

$this->getConnection()->query(
    $select->crossUpdateFromSelect(
        array('order_grid' => $this->getTable('sales/order_grid'))
    )
);

$this->endSetup();