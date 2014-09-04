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
class Magpleasure_Adminlogger_Block_Adminhtml_Adminlogger_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _helper()
    {
        return Mage::helper('adminlogger');
    }

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'log_id';
        $this->_blockGroup = 'adminlogger';
        $this->_controller = 'adminhtml_adminlogger';

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');
    }

    public function getHeaderText()
    {
        return $this->__("Summary");
    }

}