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
class AW_Betterthankyoupage_Model_AdminhtmlSystemConfigSourceCmsBlock {
    
    /**
     * 
     */
    private $__options = null;
    
    
    /**
     * 
     */
    public function toOptionArray() {
        if (!$this->__options) {
            $__options = array(
                array(
                    'label' => Mage::helper('betterthankyoupage')->__('--Please Select--'),
                    'value' => ''
                )
            );
            
            $__collection = Mage::getResourceModel('cms/block_collection');
            
            $__titleIndex = array();
            foreach ( $__collection as $__item ) {
                if ( !isset($__titleIndex[ $__item->getTitle() ]) ) {
                    $__titleIndex[ $__item->getTitle() ] = 0;
                }
                
                $__titleIndex[ $__item->getTitle() ]++;
            }
            
            foreach ( $__collection as $__item ) {
                array_push(
                    $__options,
                    array(
                        'label' => $__item->getTitle() . ( $__titleIndex[$__item->getTitle()] > 1 ? ' (' . $__item->getBlockId() . ')' : '' ),
                        'value' => $__item->getBlockId()
                    )
                );
            }
            
            $this->__options = & $__options;
            unset($__options);
        }
        
        return $this->__options;
    }
}