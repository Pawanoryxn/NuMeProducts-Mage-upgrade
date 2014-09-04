<?php
/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Magpleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Magpleasure
 * @package    Magpleasure_Adminlogger
 * @version    1.0.2
 * @copyright  Copyright (c) 2012-2013 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */
class Magpleasure_Adminlogger_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{
    const MYSQL_ZEND_DATE_FORMAT = 'yyyy-MM-dd HH:mm:ss';

    public function _construct()
    {
        $this->_init('adminlogger/log', 'log_id');
    }

    public function clearLog($keepDays)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $select = new Zend_Db_Select($writeAdapter);

        $dateTime = new Zend_Date();
        $dateTime->subDay((int)$keepDays);

        $select
            ->from($this->getMainTable())
            ->where('action_time < ?', $dateTime->toString(self::MYSQL_ZEND_DATE_FORMAT));

        $writeAdapter->beginTransaction();
        $deleteSql = $writeAdapter->deleteFromSelect($select, $this->getMainTable());
        $writeAdapter->query($deleteSql);
        $writeAdapter->commit();
        return $this;
    }

}