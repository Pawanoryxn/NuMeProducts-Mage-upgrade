<?php
/**
 * Orders Export and Import
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitexporter
 * @version      1.2.5
 * @license:     Orqsb1o5IOBC2rn5itGJF1Fmsrvozo2C91UTuZiGeO
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
$installer = $this;


$installer->startSetup();

$installer->run('

ALTER TABLE `'.$this->getTable('aitexporter_import').'` MODIFY COLUMN `status` ENUM("pending","errors","warnings","valid","complete") NOT NULL DEFAULT "pending";


');

$installer->endSetup();