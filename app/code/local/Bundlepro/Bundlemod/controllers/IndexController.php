<?php
class Bundlepro_Bundlemod_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
		$this->loadLayout();
		$listBlock = $this->getLayout()->createBlock('catalog/product_list')
            ->setTemplate('catalog/product/list.phtml')
            ->setCollection($collection);

		$this->getLayout()->getBlock('content')->append($listBlock);
		$this->renderLayout();
	  
    }
}