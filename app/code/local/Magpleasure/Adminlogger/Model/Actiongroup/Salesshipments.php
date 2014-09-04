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
class Magpleasure_Adminlogger_Model_Actiongroup_Salesshipments extends Magpleasure_Adminlogger_Model_Actiongroup_Abstract
{
    protected $_typeValue = 19;
    const ACTION_SALES_SHIPMENTS_SAVE = 1;
    const ACTION_SALES_SHIPMENTS_LOAD = 2;
    const ACTION_SALES_SHIPMENTS_SEND_TRACKING_INFO = 3;
    const ACTION_SALES_SHIPMENTS_PRINT = 4;

    public function getLabel()
    {
        return $this->_helper()->__("Sales Shipments");
    }

    protected function _getActions()
    {
        return array(
            array('value' => self::ACTION_SALES_SHIPMENTS_SAVE, 'label' => $this->_helper()->__("Create")),
            array('value' => self::ACTION_SALES_SHIPMENTS_LOAD, 'label' => $this->_helper()->__("Load")),
            array('value' => self::ACTION_SALES_SHIPMENTS_SEND_TRACKING_INFO, 'label' => $this->_helper()->__("Send Tracking Information")),
            array('value' => self::ACTION_SALES_SHIPMENTS_PRINT, 'label' => $this->_helper()->__("Print")),
        );
    }

    public function canDisplayEntity()
    {
        return true;
    }

    public function getModelType()
    {
        return 'sales/order_shipment';
    }

    public function getFieldKey()
    {
        return 'increment_id';
    }

    public function getUrlPath()
    {
        return 'adminhtml/sales_shipment/view';
    }

    public function getUrlIdKey()
    {
        return 'shipment_id';
    }

    public function getFieldPattern()
    {
        return "#%s";
    }
}