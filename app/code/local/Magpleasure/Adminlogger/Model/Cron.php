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
class Magpleasure_Adminlogger_Model_Cron
{
    const CACHE_LOCK_ID = 'admnlogger_cache_lock';

    const CRON_TIMEOUT = 3600;

    public static function run()
    {
        try {                          
            if(self::checkLock()){
                $keepDays = Mage::getStoreConfig('adminlogger/general/keep_days');
                if ($keepDays){

                    /** @var $log Magpleasure_Adminlogger_Model_Log */
                    $log = Mage::getResourceModel('adminlogger/log');
                    $log->clearLog($keepDays);

                    Mage::app()->removeCache(self::CACHE_LOCK_ID);
                }
            } else {
                echo "Admin Logger was locked";
            }
        } catch(Exception $e) {
            Mage::logException($e);
        }
    }

    public static function checkLock()
    {
        if($time = Mage::app()->loadCache(self::CACHE_LOCK_ID)){
            if((time() - $time) <= self::CRON_TIMEOUT){
                return false;
            }
        }
        Mage::app()->saveCache(time(), self::CACHE_LOCK_ID, array(), self::CRON_TIMEOUT);
        return true;
    }    
}