<?php
/**
 * @category   Oro
 * @package    Oro_Checkout
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($this->getTable('oro_checkout/attribute_cart_option'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 'null', array(
    'nullable'  => false,
    'unsigned'  => true,
), 'Attribute Option Id')
    ->addColumn('cart_label', Varien_Db_Ddl_Table::TYPE_VARCHAR, '255', array(
    'nullable'  => false,
    'default'   => '',
), 'Cart Label')
    ->addColumn('hex_color', Varien_Db_Ddl_Table::TYPE_VARCHAR, '7', array(
    'nullable'  => false,
    'default'   => '',
), 'Hex Color')
    ->addIndex($this->getIdxName('oro_checkout/attribute_cart_option', array('option_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY),
    array('option_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY))
    ->addForeignKey($installer->getFkName('oro_checkout/attribute_cart_option', 'option_id', 'eav/attribute_option', 'option_id'),
    'option_id', $installer->getTable('eav/attribute_option'), 'option_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Oro Cart Labels');

$installer->getConnection()->createTable($table);

$installer->endSetup(); 