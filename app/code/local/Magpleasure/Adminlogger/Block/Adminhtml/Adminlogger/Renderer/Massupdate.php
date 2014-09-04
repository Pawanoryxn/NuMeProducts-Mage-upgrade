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
class Magpleasure_Adminlogger_Block_Adminhtml_Adminlogger_Renderer_Massupdate
    extends Magpleasure_Adminlogger_Block_Adminhtml_Adminlogger_Renderer_Default
{

    protected $_details = array();

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("adminlogger/renderer/massupdate.phtml");
    }

    protected function _beforeToHtml()
    {
        $this->_prepareDetails();
        return parent::_beforeToHtml();
    }

    /**
     * Prepare Details
     *
     * @return Magpleasure_Adminlogger_Block_Adminhtml_Adminlogger_Renderer_Sysconfig
     */
    protected function _prepareDetails()
    {
        foreach ($this->getDetails() as $detail){
            if ($detail->getAttributeCode() == '__products__'){
                $this->setIds(unserialize($detail->getTo()));
            } else {
                $this->_details[] = $detail;
            }
        }
        return $this;
    }

    public function getProducts()
    {
        $ids = $this->getIds();
        $products = array();
        foreach ($ids as $productId){
            $product = Mage::getModel('catalog/product')->load($productId);
            $products[] = $product;
        }
        return $products;
    }

    public function getProductUrl(Mage_Catalog_Model_Product $product)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $product->getId()));
    }

    public function getUpdateDetails()
    {
        return $this->_details;
    }
}