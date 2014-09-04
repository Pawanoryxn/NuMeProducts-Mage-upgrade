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
class Magpleasure_Adminlogger_Model_Actiongroup_Abstract extends Mage_Core_Model_Abstract
    implements Magpleasure_Adminlogger_Model_Actiongroup_Interface
{

    const RENDERER_DEFAULT = 'default';
    const RENDERER_ONLYTO = 'onlyto';


    protected $_typeValue = 0;

    /**
     * Helper
     *
     * @return Magpleasure_Adminlogger_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('adminlogger');
    }

    public function getValue()
    {
        return $this->_typeValue;
    }

    public function getLabel()
    {
    }

    public function getActionLabel($value)
    {
        foreach ($this->_getActions() as $action) {
            if ($action['value'] == $value) {
                return $action['label'];
            }
        }
        return false;
    }

    public function getOptions()
    {
        $result = array();
        foreach ($this->_getActions() as $action) {
            $result[$action['value']] = $action['label'];
        }
        return $result;
    }

    public function getDetailsRenderer($type = false)
    {
        return self::RENDERER_DEFAULT;
    }

    public function canDisplayEntity()
    {
        return false;
    }

    public function getModelType()
    {
        return '';
    }

    public function getFieldKey()
    {
        return 'name';
    }

    public function getUrlPath()
    {
        return '';
    }

    public function getUrlIdKey()
    {
        return 'id';
    }

    public function getFieldPattern()
    {
        return "%s";
    }

    protected function _getActions() {}
}