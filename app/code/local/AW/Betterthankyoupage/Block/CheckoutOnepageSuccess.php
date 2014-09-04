<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Betterthankyoupage
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


/**
 *
 */
class AW_Betterthankyoupage_Block_CheckoutOnepageSuccess
    extends AW_Betterthankyoupage_Block_CheckoutOnepageSuccessCommon
{

    /**
     *
     */
    private $__sectionBlocks = array();
    private $__sortIndex     = array();

    /**
     *
     */
    public function addSectionBlock($blockAlias, $sortOrder = '') {
        $__block = $this->getLayout()->getBlock($blockAlias);

        if ( $__block ) {
            if ( $sortOrder ) $__block->setSortOrder($sortOrder);

            array_push(
                $this->__sectionBlocks,
                $__block
            );

            $__sortOrder = $__block->getSortOrder();
            if ( !isset($this->__sortIndex[$__sortOrder]) ) $this->__sortIndex[$__sortOrder] = array();
            array_push($this->__sortIndex[$__sortOrder], $__block);
        }

        return $this;
    }

    public function addSection($blockAlias, $sortOrder) {
        return $this->addSectionBlock($blockAlias, $sortOrder);
    }

    /**
     *
     */
    public function getSectionBlocks() {
        return $this->__sectionBlocks;
    }

    public function getSections() {
        return $this->getSectionBlocks();
    }

    /**
     *
     */
    public function getSortedSectionBlocks() {
        $__index = $this->__sortIndex;
        ksort($__index);

        $__sortedBlocks = array();
        foreach ( $__index as $__blocks ) {
            foreach ( $__blocks as $__block ) {
                array_push($__sortedBlocks, $__block);
            }
        }

        return $__sortedBlocks;
    }

    public function getSortedSections() {
        return $this->getSortedSectionBlocks();
    }

    /**
     *
     */
    protected function _beforeToHtml() {
        $__result = parent::_beforeToHtml();
        if ( $this->isModuleEnabled() ) {
            /* AW_Sarp2 Compatibility */
            if (Mage::helper('betterthankyoupage')->isSarp2Installed()) {
                Mage::helper('betterthankyoupage/sarp2')->addSectionListToSuccessPage($this);
            }
            $this->setTemplate('betterthankyoupage/onepage_success.phtml');
        }
        return $__result;
    }

    /**
     *
     */
    public function isModuleEnabled() {
        return ( Mage::getStoreConfig('betterthankyoupage/general/module_enabled') ? true : false );
    }
}