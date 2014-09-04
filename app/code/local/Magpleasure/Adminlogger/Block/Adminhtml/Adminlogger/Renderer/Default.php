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
class Magpleasure_Adminlogger_Block_Adminhtml_Adminlogger_Renderer_Default extends Mage_Adminhtml_Block_Template
{
    protected $_log;
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("adminlogger/renderer/default.phtml");
    }

    /**
     * @return Magpleasure_Adminlogger_Model_Log
     */
    public function getLog()
    {
        return $this->_log;
    }

    public function setLog($value)
    {
        $this->_log = $value;
        return $this;
    }

    public function getDetails()
    {
        return $this->getLog()->getDetailsCollection();
    }
}