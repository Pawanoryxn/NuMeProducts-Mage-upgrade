<?php

class Atypicalbrands_Randomrelatedp_Block_Catalog_Product_List_Related extends Mage_Catalog_Block_Product_List_Related{

	//Made it return related products in a random order
	protected function _prepareData()
    {
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        $this->_itemCollection = $product->getRelatedProductCollection()
            ->addAttributeToSelect('required_options')
            ->setPositionOrder()
            ->addStoreFilter()
        ;

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
//        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);
		$this->_itemCollection->getSelect()->order('rand()');
        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }



}