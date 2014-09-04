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
class Magpleasure_Adminlogger_Model_Observer_Systemdesign extends Magpleasure_Adminlogger_Model_Observer
{

    public function SystemDesignLoad($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('systemdesign')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Systemdesign::ACTION_DESIGN_LOAD,
            Mage::app()->getRequest()->getParam('id')
        );
    }

    public function SystemDesignSave($event)
    {
        $design = $event->getObject();
        $log = $this->createLogRecord(
            $this->getActionGroup('systemdesign')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Systemdesign::ACTION_DESIGN_SAVE,
            $design->getId()
        );
        if ($log){
            $log->addDetails(
                $this->_helper()->getCompare()->diff($design->getData(), $design->getOrigData())
            );
        }
    }

    public function SystemDesignDelete($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('systemdesign')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Systemdesign::ACTION_DESIGN_DELETE
        );
    }
}