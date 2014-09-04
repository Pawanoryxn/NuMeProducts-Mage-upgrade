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
class Magpleasure_Adminlogger_Model_System_Config_Source_Users extends Mage_Core_Model_Abstract
{
    /**
     * Helper
     *
     * @return Magpleasure_Adminlogger_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('adminlogger');
    }

    public function toOptionArray()
    {
        $result = array();

        $result[] = array('value'=>Magpleasure_Adminlogger_Model_Log::SYSTEM_LOG_ALL_USERS, 'label'=>$this->_helper()->__("All Users"));

        foreach ($this->_helper()->getUsers() as $key=>$value){
            $result[] = array('value'=>$key, 'label'=>$value);
        }
        return $result;
    }
}