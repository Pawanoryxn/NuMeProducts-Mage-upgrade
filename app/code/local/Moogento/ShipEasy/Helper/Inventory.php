<?php

class Moogento_ShipEasy_Helper_Inventory extends Mage_Core_Helper_Abstract
{
    protected $_result = null;

    protected function _addValueToResult($bool)
    {
        if (is_null($this->_result)) {
            $this->_result = ($bool) ? 1 : -1;
        } else {
            if ($bool) {
                $this->_result = ($this->_result === 1) ? $this->_result : 0;
            } else {
                $this->_result = ($this->_result === -1) ? $this->_result : 0;
            }
        }

        return $this->_result;
    }

    protected function _checkItemQty($stockItem, $qty)
    {
        if (!$stockItem->getManageStock()) {
            return true;
        }

        if ($stockItem->getQty() - $qty < 0) {
            switch ($stockItem->getBackorders()) {
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY:
                case Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY:
                    break;
                default:
                    return false;
                    break;
            }
        }
        return true;

    }

    public function checkAvailability(&$array)
    {
        $this->_result = null;

        foreach($array as $productId => $requestedQty)
        {
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);

            if (!$stockItem->getItemId()) {
                $newResultValue = $this->_addValueToResult(false);
                if ($newResultValue === 0) {
                    return $newResultValue;
                }
                continue;
            }

            $newResultValue = $this->_addValueToResult(
                $this->_checkItemQty($stockItem, $requestedQty)
            );

            if ($newResultValue === 0) {
                return $newResultValue;
            }
        }

        return $this->_result;
    }
}