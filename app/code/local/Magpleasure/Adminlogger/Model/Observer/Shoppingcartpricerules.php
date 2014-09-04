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
class Magpleasure_Adminlogger_Model_Observer_Shoppingcartpricerules extends Magpleasure_Adminlogger_Model_Observer
{

    public function PriceRulesLoad($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('shoppingcartpricerules')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Shoppingcartpricerules::ACTION_PRICE_RULES_LOAD,
            Mage::app()->getRequest()->getParam('id')
        );
    }

    public function PriceRulesSave($event)
    {
        $priceRule = $event->getRule();
        $log = $this->createLogRecord(
            $this->getActionGroup('shoppingcartpricerules')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Shoppingcartpricerules::ACTION_PRICE_RULES_SAVE,
            $priceRule->getId()
        );
        if ($log){
            $log->addDetails(
                $this->_helper()->getCompare()->diff($priceRule->getData(), $priceRule->getOrigData())
            );
        }
    }

    public function PriceRulesDelete($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('shoppingcartpricerules')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Shoppingcartpricerules::ACTION_PRICE_RULES_DELETE
        );
    }
}