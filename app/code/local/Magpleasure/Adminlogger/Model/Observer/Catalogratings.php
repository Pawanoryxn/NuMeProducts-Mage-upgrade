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
class Magpleasure_Adminlogger_Model_Observer_Catalogratings extends Magpleasure_Adminlogger_Model_Observer
{

    public function CatalogRatingsLoad($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('catalogratings')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Catalogratings::ACTION_RATINGS_LOAD,
            Mage::app()->getRequest()->getParam('id')
        );
    }

    public function CatalogRatingsSave($event)
    {
        $rating = $event->getObject();
        $log = $this->createLogRecord(
            $this->getActionGroup('catalogratings')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Catalogratings::ACTION_RATINGS_SAVE,
            $rating->getId()
        );
        if ($log){
            $log->addDetails(
                $this->_helper()->getCompare()->diff($rating->getData(), $rating->getOrigData())
            );
        }
    }

    public function CatalogRatingsDelete($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('catalogratings')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Catalogratings::ACTION_RATINGS_DELETE
        );
    }
}