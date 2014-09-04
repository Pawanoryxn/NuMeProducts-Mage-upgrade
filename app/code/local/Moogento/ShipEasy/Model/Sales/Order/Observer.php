<?php

class Moogento_ShipEasy_Model_Sales_Order_Observer
{
    protected function _getBaseOrderCost(Mage_Sales_Model_Order $order)
    {
        $baseCost = 0;
        foreach($order->getAllVisibleItems() as $item) {
            $baseCost += $item->getBaseCost() * $item->getQtyOrdered();
        }

        return $baseCost;
    }

    protected function _getAdditionalCharges($order)
    {
        $fixedAmount = Mage::getStoreConfig('moogento_shipeasy/charges/fixed_amount');
        $percentAmount = Mage::getStoreConfig('moogento_shipeasy/charges/percent_amount');
        return $order->getBaseGrandTotal() * $percentAmount / 100 + $fixedAmount;
    }

    public function orderBeforeSave($observer)
    {
        $order = $observer->getOrder();
        $_baseAdditionalCharges = 0;

        if (is_null($order->getBaseAdditionalCharges())) {
            $_baseAdditionalCharges = $this->_getAdditionalCharges($order);
            $order->setBaseAdditionalCharges($_baseAdditionalCharges);
        }

        $_baseGrandTotal     = $order->getBaseGrandTotal();
        $_baseShippingCost   = $order->getBaseShippingCost();
        $_baseCost           = $this->_getBaseOrderCost($order);

        $order->setBaseProfit($_baseGrandTotal - $_baseShippingCost - $_baseCost - $_baseAdditionalCharges);

        /**
         * We ship orders - set up Shipped status
         */
        if ($order->getStatusToSet() && $order->getState() == Mage_Sales_Model_Order::STATE_NEW && $order->getIsInProcess()) {
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, $order->getStatusToSet(), '', true);
        }

        return $this;
    }

    public function shipmentDeleteAfter($observer)
    {
        $track = $observer->getDataObject();
        $shipment = $track->getShipment();
        $this->shipmentAfterSave(
            new Varien_Object(array('shipment' => $shipment))
        );
    }

    public function orderAfterSave($observer)
    {
        $order = $observer->getOrder();
        $sku = false;
        $name = false;
        $_allowedProductTypes = array('bundle', 'simple', 'virtual', 'downloadable');
        foreach($order->getItemsCollection() as $orderItem) {
            $productType = $orderItem->getProductType();
            if (in_array($productType, $_allowedProductTypes)) {
                $sku = (!$sku) ? $orderItem->getSku() : $sku . ',' . $orderItem->getSku();
                $name = (!$name) ? $orderItem->getName() : $name . ',' . $orderItem->getName();
            }
        }

        if ($sku) {
            Mage::getResourceSingleton('moogento_shipeasy/sales_order')->updateGridRow(
                $order,
                'product_skus',
                $sku
            );
        }

        if ($name) {
            Mage::getResourceSingleton('moogento_shipeasy/sales_order')->updateGridRow(
                $order,
                'product_names',
                $name
            );
        }
    }

    public function shipmentAfterSave($observer)
    {
        $shipment = $observer->getShipment();
        $order = $shipment->getOrder();
        $tracks = $order->getTracksCollection();

        $trackLinks = array();
        if (count($tracks)) {
            foreach($tracks as $track) {
                $trackLinks[] = array(
                    'link' => Mage::helper('moogento_shipeasy/track')->getTrackUrl($track),
                    'title' => $track->getTitle()
                );
            }
            Mage::getResourceSingleton('moogento_shipeasy/sales_order')->updateGridRow(
                $order,
                'tracking_number',
                serialize($trackLinks)
            );
        }
    }
}