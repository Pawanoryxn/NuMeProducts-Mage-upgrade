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
class Magpleasure_Adminlogger_Model_Actiongroup_Managestores extends Magpleasure_Adminlogger_Model_Actiongroup_Abstract
{
    protected $_typeValue = 37;
    const ACTION_MANAGE_STORES_LOAD = 1;
    const ACTION_MANAGE_STORES_SAVE = 2;
    const ACTION_MANAGE_STORES_DELETE = 3;

    public function getLabel()
    {
        return $this->_helper()->__("Manage Stores");
    }

    protected function _getActions()
    {
        return array(
            array('value' => self::ACTION_MANAGE_STORES_LOAD, 'label' => $this->_helper()->__("Load")),
            array('value' => self::ACTION_MANAGE_STORES_SAVE, 'label' => $this->_helper()->__("Save")),
            array('value' => self::ACTION_MANAGE_STORES_DELETE, 'label' => $this->_helper()->__("Delete")),
        );
    }

    public function canDisplayEntity()
    {
        return true;
    }

    public function getModelType()
    {
        return 'core/store_group';
    }

    public function getFieldKey()
    {
        return 'name';
    }

    public function getUrlPath()
    {
        return 'adminhtml/system_store/editGroup';
    }

    public function getUrlIdKey()
    {
        return 'group_id';
    }

}
