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
class Magpleasure_Adminlogger_Model_Observer_Producttaxclasses extends Magpleasure_Adminlogger_Model_Observer
{

    public function ProductTaxClassesLoad($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('producttaxclasses')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Producttaxclasses::ACTION_PRODUCT_TAX_CLASSES_LOAD,
            Mage::app()->getRequest()->getParam('id')
        );
    }

    public function ProductTaxClassesSave($event)
    {
        if (Mage::app()->getRequest()->getPost('class_type') == Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT) {
            $taxClass = $event->getObject();
            $log = $this->createLogRecord(
                $this->getActionGroup('producttaxclasses')->getValue(),
                Magpleasure_Adminlogger_Model_Actiongroup_Producttaxclasses::ACTION_PRODUCT_TAX_CLASSES_SAVE,
                $taxClass->getId()
            );
            $saveData = $taxClass->getData();
            $id = Mage::app()->getRequest()->getPost('class_id');
            if (!$taxClass->getOrigData() && $id) {
                $taxClass->load($id);
            }
            $taxClass->addData($saveData);
            if ($log){
                $log->addDetails(
                    $this->_helper()->getCompare()->diff($taxClass->getData(), $taxClass->getOrigData())
                );
            }
        }
    }

    public function ProductTaxClassesDelete($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('producttaxclasses')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Producttaxclasses::ACTION_PRODUCT_TAX_CLASSES_DELETE
        );
    }
}