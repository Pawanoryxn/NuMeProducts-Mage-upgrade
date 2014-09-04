<?php
 
$this->startSetup();


$fieldsSql = 'SHOW COLUMNS FROM ' . $this->getTable('salesrule/rule');
$cols = $this->getConnection()->fetchCol($fieldsSql);
if (!in_array('publisher', $cols)){
    $this->getConnection()->addColumn($this->getTable('salesrule/rule'), 'publisher', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(225)');
}

if (!in_array('campaign_id', $cols)){
     $this->getConnection()->addColumn($this->getTable('salesrule/rule'), 'campaign_id', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(225)');
}


if (!in_array('campaign_type', $cols)){
    $this->getConnection()->addColumn($this->getTable('salesrule/rule'), 'campaign_type', Varien_Db_Ddl_Table::TYPE_VARCHAR.'(50)');
}
    

if (!in_array('external_url', $cols)){
    $this->getConnection()->addColumn($this->getTable('salesrule/rule'), 'external_url', Varien_Db_Ddl_Table::TYPE_TEXT);
}

if (!in_array('created_ats', $cols)){
    $this->getConnection()->addColumn($this->getTable('salesrule/rule'), 'created_ats', Varien_Db_Ddl_Table::TYPE_TEXT);
}

if (!in_array('campaign_start_date', $cols)){
     $this->getConnection()->addColumn($this->getTable('salesrule/rule'), 'campaign_start_date', Varien_Db_Ddl_Table::TYPE_DATE);
}

if (!in_array('campaign_end_date', $cols)){
     $this->getConnection()->addColumn($this->getTable('salesrule/rule'), 'campaign_end_date', Varien_Db_Ddl_Table::TYPE_DATE);
}

if (!in_array('set_flatrate_by_region', $cols)){
	$this->run("ALTER TABLE `{$this->getTable('salesrule/rule')}` ADD `set_flatrate_by_region` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0'"); 
}

if (!in_array('set_flatrate_shipping_us', $cols)){
	$this->run("ALTER TABLE `{$this->getTable('salesrule/rule')}` ADD `set_flatrate_shipping_us` DECIMAL(12,2) UNSIGNED DEFAULT NULL");
	   
}
if (!in_array('set_flatrate_shipping_ca', $cols)){
	$this->run("ALTER TABLE `{$this->getTable('salesrule/rule')}` ADD `set_flatrate_shipping_ca` DECIMAL(12,2) UNSIGNED DEFAULT NULL");
 
}
if (!in_array('coupon_shipping_rate', $cols)){
        $this->run("ALTER TABLE `{$this->getTable('salesrule/rule')}` ADD `coupon_shipping_rate` mediumtext");    
}

$this->endSetup();