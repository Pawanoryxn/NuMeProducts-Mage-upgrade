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
class Magpleasure_Adminlogger_Model_Observer_Managestoreviews extends Magpleasure_Adminlogger_Model_Observer
{

    public function ManageStoreViewsLoad($event)
    {
        if (Mage::registry('store_type')){
            return $this;
        }

        $this->createLogRecord(
            $this->getActionGroup('managestoreviews')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Managestoreviews::ACTION_MANAGE_STORE_VIEWS_LOAD,
            Mage::app()->getRequest()->getParam('store_id')
        );
    }

    public function ManageStoreViewsSave($event)
    {
        $storeView = $event->getStore();
        $log = $this->createLogRecord(
            $this->getActionGroup('managestoreviews')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Managestoreviews::ACTION_MANAGE_STORE_VIEWS_SAVE,
            $storeView->getId()
        );
        if ($log){
            $log->addDetails(
                $this->_helper()->getCompare()->diff($storeView->getData(), $storeView->getOrigData())
            );
        }
    }

    public function ManageStoreViewsDelete($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('managestoreviews')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Managestoreviews::ACTION_MANAGE_STORE_VIEWS_DELETE
        );
    }
}