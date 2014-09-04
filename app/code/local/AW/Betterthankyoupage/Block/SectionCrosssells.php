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
class AW_Betterthankyoupage_Block_SectionCrosssells extends Mage_Checkout_Block_Cart_Crosssell {
    
    /**
     * 
     */
    public function getOrder() {
        return Mage::helper('betterthankyoupage/successPage')->getOrder();
    }
    
    
    
    /**
     * 
     */
    public function getOrders() {
        return Mage::helper('betterthankyoupage/successPage')->getOrders();
    }
    
    
    
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
        return ( Mage::getStoreConfig('betterthankyoupage/crosssell_block_section/display_section') ? true : false );
    }
    
    
    /**
     * 
     */
    public function getSortOrder() {
        return intval( Mage::getStoreConfig('betterthankyoupage/crosssell_block_section/section_sort_order') );
    }
    
    
    /**
     * 
     */
    private $__items = null;
    
    
    /**
     * 
     */
    protected function __loadData() {
        $this->__items = array();
        
        $__limit = $this->__getLimit();
        if ( $__limit > 0 ) {
            $__orders = array();
            
            if ( Mage::helper('betterthankyoupage/successPage')->isSingleOrder() ) {
                $__order = $this->getOrder();
                if ( $__order ) $__orders = array($__order);
            }
            else {
                $__orders = $this->getOrders();
            }
            
            $__counter = 0;
            $__takenProductIDs = array();
            foreach ( $__orders as $__order ) {
                foreach ( $__order->getAllItems() as $__orderItem ) {
                    $__product = Mage::getModel('catalog/product')->load($__orderItem->getProductId());
                    
                    $__crossSellCollection = $__product->getCrossSellProductCollection()
                        ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                        ->setPositionOrder()
                        ->addStoreFilter()
                    ;
                    $this->_addProductAttributesAndPrices($__crossSellCollection);
                    Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($__crossSellCollection);
                    Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($__crossSellCollection);
                    
                    $__crossSellCollection->load();
                    
                    foreach ($__crossSellCollection as $__crossSellProduct) {
                        if ( in_array($__crossSellProduct->getId(), $__takenProductIDs) ) {
                            continue;
                        }
                        else {
                            array_push($__takenProductIDs, $__crossSellProduct->getId());
                        }
                        
                        $__crossSellProduct->setDoNotUseCategoryId(true);
                        array_push($this->__items, $__crossSellProduct);
                        $__counter++;
                        if ( $__counter >= $__limit ) break;
                    }
                    
                    if ( $__counter >= $__limit ) break;
                }
                if ( $__counter >= $__limit ) break;
            }
        }
        
        return $this;
    }
    
    
    /**
     * 
     */
    protected function __getLimit() {
        return intval( Mage::getStoreConfig('betterthankyoupage/crosssell_block_section/number_of_items_to_show') );
    }
    
    
    /**
     * 
     */
    protected function _prepareData() {
        return $this->__loadData();
    }
    
    
    /**
     * 
     */
    public function getItems() {
        if ( !$this->__items ) {
            $this->__loadData();
        }
        
        return $this->__items;
    }
}