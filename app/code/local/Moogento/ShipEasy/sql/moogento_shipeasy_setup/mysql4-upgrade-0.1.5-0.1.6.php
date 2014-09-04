<?php

$installer = $this;
$this->startSetup();

try {
    $statusTable = $installer->getTable('sales/order_status');

    $installer->getConnection()->insertArray(
        $statusTable,
        array('status', 'label'),
        array(
            array(
                'status' => 'shipped',
                'label'  => 'Shipped'
            )
        )
    );
} catch (Mage_Core_Exception $ex) {
    /*
     * Do nothing - old magento version. Statues don't have own tables
     */
}
$this->endSetup();