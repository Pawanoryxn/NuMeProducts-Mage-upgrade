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

class Magpleasure_Adminlogger_Model_Observer_Salescreditmemos extends Magpleasure_Adminlogger_Model_Observer
{

    public function SalesCreditmemosLoad($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('salescreditmemos')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Salescreditmemos::ACTION_SALES_CREDITMEMOS_LOAD,
            Mage::app()->getRequest()->getParam('creditmemo_id')
        );
    }

    public function SalesCreditmemosEmail($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('salescreditmemos')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Salescreditmemos::ACTION_SALES_CREDITMEMOS_EMAIL,
            Mage::app()->getRequest()->getParam('creditmemo_id')
        );
    }

    public function SalesCreditmemosPrint($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('salescreditmemos')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Salescreditmemos::ACTION_SALES_CREDITMEMOS_PRINT,
            Mage::app()->getRequest()->getParam('creditmemo_id')
        );
    }

    public function SalesCreditmemosSave($event)
    {
        $creditmemo = $event->getCreditmemo();
        if ($creditmemo) {
            $log = $this->createLogRecord(
                $this->getActionGroup('salescreditmemos')->getValue(),
                Magpleasure_Adminlogger_Model_Actiongroup_Salescreditmemos::ACTION_SALES_CREDITMEMOS_CREATE,
                $creditmemo->getId()
            );

            if ($log){
                $log->addDetails(
                    $this->_helper()->getCompare()->diff($creditmemo->getData(), $creditmemo->getOrigData())
                );
            }
        }
    }

}
