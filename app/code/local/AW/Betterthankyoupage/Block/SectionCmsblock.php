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
class AW_Betterthankyoupage_Block_SectionCmsblock extends Mage_Core_Block_Template {
    
    /**
     * 
     */
    protected function _toHtml() {
        if ( Mage::getStoreConfig('betterthankyoupage/general/module_enabled') ) {
            return parent::_toHtml();
        }
        
        return null;
    }
    
    
    /**
     * 
     */
    public function shouldDisplaySection() {
        return ( Mage::getStoreConfig('betterthankyoupage/cms_block_section/display_section') ? true : false );
    }
    
    
    /**
     * 
     */
    public function getSortOrder() {
        return intval( Mage::getStoreConfig('betterthankyoupage/cms_block_section/section_sort_order') );
    }
    
    
    /**
     * 
     */
    public function getCmsBlock() {
        $__block = null;
        
        $__blockID = Mage::getStoreConfig('betterthankyoupage/cms_block_section/cms_block');
        if ( $__blockID ) {
            $__block = Mage::getModel('cms/block')
                ->load($__blockID)
            ;
        }
        
        return $__block;
    }
    
    
    /**
     * 
     */
    public function getCmsBlockHtml() {
        $__html = '';
        
        $__block = $this->getCmsBlock();
        if ( !is_null($__block) and $__block->getIsActive()) {
            $__templateProcessor = Mage::helper('cms')->getBlockTemplateProcessor();
            $__html = $__templateProcessor->filter($__block->getContent());
        }
        
        return $__html;
    }
}