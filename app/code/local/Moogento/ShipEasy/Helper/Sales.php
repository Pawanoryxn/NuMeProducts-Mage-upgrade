<?php

class Moogento_ShipEasy_Helper_Sales extends Mage_Core_Helper_Abstract
{
    public function initInvoice($order)
    {
        if (!$order instanceof Mage_Sales_Model_Order) {
            $order = Mage::getModel('sales/order')->load($order);
        }

        if (!$order->getId()) {
            return false;
        }

        if (!$order->canInvoice()) {
            return false;
        }

        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
        if (!$invoice->getTotalQty()) {
            return false;
        }

        return $invoice;
    }

    public function initShipment($order)
    {
        if (!$order instanceof Mage_Sales_Model_Order) {
            $order = Mage::getModel('sales/order')->load($order);
        }

        if (!$order->getId()) {
            return false;
        }

        if ($order->getForcedDoShipmentWithInvoice()) {
            return false;
        }

        if (!$order->canShip()) {
            return false;
        }

        $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment(array());
        return $shipment;
    }

    public function saveShipment($shipment, $status=false)
    {
        $shipment->getOrder()->setIsInProcess(true);
        $order = $shipment->getOrder();
        if ($status) {
            $order->setStatusToSet($status);
        }
        
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($order)
            ->save();

        return $this;
    }

    public function prepareShipment($invoice)
    {
        $shipment = Mage::getModel('sales/service_order', $invoice->getOrder())->prepareShipment(array());
        if (!$shipment->getTotalQty()) {
            return false;
        }
        $shipment->register();
        return $shipment;
    }
}