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
class Magpleasure_Adminlogger_Model_Observer_Adminpermissionusers extends Magpleasure_Adminlogger_Model_Observer
{

    public function AdminPermissionUsersLoad($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('adminpermissionusers')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Adminpermissionusers::ACTION_ADMIN_PERMISSION_USERS_LOAD,
            Mage::app()->getRequest()->getParam('user_id')
        );
    }

    public function AdminPermissionUsersSave($event)
    {
        $adminUser = $event->getObject();
        $log = $this->createLogRecord(
            $this->getActionGroup('adminpermissionusers')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Adminpermissionusers::ACTION_ADMIN_PERMISSION_USERS_SAVE,
            $adminUser->getId()
        );
        if ($log){
            $log->addDetails(
                $this->_helper()->getCompare()->diff($adminUser->getData(), $adminUser->getOrigData())
            );
        }
    }

    public function AdminPermissionUsersDelete($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('adminpermissionusers')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Adminpermissionusers::ACTION_ADMIN_PERMISSION_USERS_DELETE
        );
    }
}