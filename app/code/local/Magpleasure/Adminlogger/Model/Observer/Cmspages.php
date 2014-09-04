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
class Magpleasure_Adminlogger_Model_Observer_Cmspages extends Magpleasure_Adminlogger_Model_Observer
{
    public function CmsPagesLoad($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('cmspages')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Cmspages::ACTION_PAGES_LOAD,
            Mage::app()->getRequest()->getParam('page_id')
        );
    }

    public function CmsPagesSave($event)
    {
        $pages = $event->getObject();
        $log = $this->createLogRecord(
            $this->getActionGroup('cmspages')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Cmspages::ACTION_PAGES_SAVE,
            $pages->getId()
        );
        if ($log){
            $log->addDetails(
                $this->_helper()->getCompare()->diff($pages->getData(), $pages->getOrigData())
            );
        }

    }

    public function CmsPagesDelete($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('cmspages')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Cmspages::ACTION_PAGES_DELETE
        );
    }
}
