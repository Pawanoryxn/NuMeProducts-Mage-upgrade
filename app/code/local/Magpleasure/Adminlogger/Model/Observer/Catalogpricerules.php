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
class Magpleasure_Adminlogger_Model_Observer_Catalogpricerules extends Magpleasure_Adminlogger_Model_Observer
{

    public function CatalogPriceRulesLoad($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('catalogpricerules')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Catalogpricerules::ACTION_PRICE_RULES_LOAD,
            Mage::app()->getRequest()->getParam('id')
        );
    }

    public function CatalogPriceRulesSave($event)
    {
        $rule = $event->getRule();
        $log = $this->createLogRecord(
            $this->getActionGroup('catalogpricerules')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Catalogpricerules::ACTION_PRICE_RULES_SAVE,
            $rule->getId()
        );
        if ($log){
            $log->addDetails(
                $this->_helper()->getCompare()->diff($rule->getData(), $rule->getOrigData())
            );
        }
    }

    public function CatalogPriceRulesDelete($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('catalogpricerules')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Catalogpricerules::ACTION_PRICE_RULES_DELETE
        );
    }

    public function CatalogPriceRulesApply($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('catalogpricerules')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Catalogpricerules::ACTION_PRICE_RULES_APPLY
        );
    }
}