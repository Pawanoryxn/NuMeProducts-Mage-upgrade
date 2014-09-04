<?php

class Moogento_ShipEasy_Block_Adminhtml_Sales_Order_Grid_Skus extends Mage_Adminhtml_Block_Template
{
    protected $_xmlPathFillColor = 'moogento_shipeasy/grid/product_skus_fill_color';
    protected $_xmlPathColorUnavailable = 'moogento_shipeasy/grid/product_skus_fully_unavailable';
    protected $_xmlPathColorFullyAvailable = 'moogento_shipeasy/grid/product_skus_fully_available';
    protected $_xmlPathColorPartiallyAvailable = 'moogento_shipeasy/grid/product_skus_partially_available';

    protected $_xmlPathTruncateText = 'moogento_shipeasy/grid/product_skus_truncate';
    protected $_xmlPathTruncateLength = 'moogento_shipeasy/grid/product_skus_x_truncate';

    protected $_simpleProductTypes = array(
        'simple',
        'virtual',
        'downloadable'
    );

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('moogento/sales/order/grid/skus.phtml');
    }

    protected function _getAvailableColor()
    {
        if (!Mage::getStoreConfig($this->_xmlPathFillColor)) {
            return 'transparent';
        }

        $itemsQtyArray = array();
        foreach($this->getItemsCollection() as $item) {
            if (in_array($item->getProductType(), $this->_simpleProductTypes)) {
                $productId = $item->getProductId();
                $qty = $item->getQtyOrdered();
                if ($item->getParentItem() && ($item->getParentItem()->getProductType() == 'bundle')) {
                    $qty *= $item->getParentItem()->getQtyOrdered();
                }

                if (isset($itemsQtyArray[$productId])) {
                    $itemsQtyArray[$productId] += $qty;
                } else {
                    $itemsQtyArray[$productId] = $qty;
                }
            }
        }

        $result = -1;
        if (count($itemsQtyArray)) {
            $result = Mage::helper('moogento_shipeasy/inventory')->checkAvailability($itemsQtyArray);
        }


        switch ($result) {
            case -1:
                $color = Mage::getStoreConfig($this->_xmlPathColorUnavailable);
                break;
            case 1:
                $color = Mage::getStoreConfig($this->_xmlPathColorFullyAvailable);
                break;
            case 0:
                $color = Mage::getStoreConfig($this->_xmlPathColorPartiallyAvailable);
                break;
        }

        return $color;
    }

    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }

    protected function _truncate($sku, $additionalSymbolCounts = 0)
    {
        if (!Mage::getStoreConfigFlag($this->_xmlPathTruncateText)) {
            return $sku;
        }

        $truncatePosition = (int)Mage::getStoreConfig($this->_xmlPathTruncateLength) - $additionalSymbolCounts;
        if ($truncatePosition < strlen($sku)) {
            return substr($sku, 0, $truncatePosition) . '...';
        } else {
            return $sku;
        }
    }

    protected function _isBundleProduct($item)
    {
        $result = false;



        if ($item->getProductType() == 'bundle') {
            $result = true;
        }
        return $result;
    }

    protected function _isSimpleWeDisplay($item)
    {
        $result = false;

        /*
         * If it's just simple or virtual product
         */

        if (
            in_array($item->getProductType(), $this->_simpleProductTypes) &&
            (
                !$item->getParentItem() ||
                ($item->getParentItem()->getProductType() == 'configurable')
            )
        ) {
            $result = true;
        }

        return $result;
    }
}