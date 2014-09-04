<?php

$installer = $this;
$installer->startSetup();

/**
 * Create table 'atfeed/feed'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('atfeed/feed'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Feed Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Name')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Store Id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Type')
    ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Filname')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Description')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
    ), 'Is Active')
    ->addColumn('ftp_host', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'FTP Host')
    ->addColumn('ftp_user', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'FTP Username')
    ->addColumn('ftp_pass', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'FTP Password')
    ->addColumn('ftp_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'FTP Path')
    ->addColumn('ftp_filename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'FTP Filename')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
    ), 'Category Conditions')
    ->addColumn('actions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
    ), 'Product Conditions')
    ->addColumn('product_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Product Ids')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    ), 'Website Id')
    ->addIndex($installer->getIdxName('atfeed/feed', array('is_active')),
        array('is_active'))
    ->setComment('Feed');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()->newTable($this->getTable('atfeed/feedattribute'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'nullable' => false,
        'unsigned' => true,
        'primary' => true
    ), 'Id')
    ->addColumn('feed_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable' => false,
        'unsigned' => true,
    ), 'Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false,
        'unsigned' => true
    ), 'Attribute Id')
    ->addColumn('feed_attribute', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Feed Attribute')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true
    ), 'Type Id')
    ->addForeignKey(
        $installer->getFkName(
            'atfeed/feedattribute',
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id',
        $this->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )

    ->setComment('Feed Attributes link Product Attributes');
$installer->getConnection()->createTable($table);


$table = $installer->getConnection()->newTable($this->getTable('atfeed/category'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'nullable' => false,
        'unsigned' => true,
        'primary' => true
    ), 'Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Name')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable' => true,
        'unsigned' => true
    ), 'Parent Id')
    ->addColumn('category_level', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Category Level')
    ->setComment('Feed Attributes link Product Attributes');
$installer->getConnection()->createTable($table);
