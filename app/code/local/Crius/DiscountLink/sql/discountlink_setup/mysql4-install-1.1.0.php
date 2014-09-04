<?php
/**
 * Crius
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt
 *
 * @category   Crius
 * @package    Crius_DiscountLink
 * @copyright  Copyright (c) 2013 Crius (http://www.criuscommerce.com)
 * @license    http://www.criuscommerce.com/CRIUS-LICENSE.txt
 */
 
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('salesrule'),
    'coupon_redirect_url', "VARCHAR(255) DEFAULT NULL");

$installer->endSetup();
