<?php

class Moogento_ShipEasy_Model_Convert_Adapter_Shipment extends Mage_Eav_Model_Convert_Adapter_Entity
{
    public function parse()
    {
        $batchModel = Mage::getSingleton('dataflow/batch');
        /* @var $batchModel Mage_Dataflow_Model_Batch */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();

        foreach ($importIds as $importId) {
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();

            $this->saveRow($importData);
        }
    }

    protected function _shipOrder($order, $shippingCost = false, $trackingInfo = array())
    {
        try {
            if ($shipment = Mage::helper('moogento_shipeasy/sales')->initShipment($order)) {
                $shipment->register();
                $shipment->setEmailSent(true);
                $shipment->getOrder()->setCustomerNoteNotify(true);

                if ($shippingCost) {
                    $shipment->getOrder()->setBaseShippingCost($shippingCost);
                }

                foreach($trackingInfo as $trackText) {
                    $track = Mage::helper('moogento_shipeasy/track')->getTrackModel(trim($trackText));
                    $shipment->addTrack($track);
                }

                Mage::helper('moogento_shipeasy/sales')->saveShipment($shipment, 'shipped');
                $shipment->sendEmail(true, '');
            } else {
                $message = Mage::helper('moogento_shipeasy')->__('Skipping import row, order "%s" can not be shipped.', $order->getIncrementId());
                Mage::throwException($message);
            }
        } catch(Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }

    public function saveRow(array $importData)
    {
        $orderIncrementIdField = Mage::getStoreConfig('moogento_shipeasy/import/order_increment_id');
        $orderIncrementIdField = ($orderIncrementIdField) ? $orderIncrementIdField : 'order_increment_id';

        if (!isset($importData[$orderIncrementIdField]) || empty($importData[$orderIncrementIdField])) {
            $message = Mage::helper('moogento_shipeasy')->__('Skipping import row, required field "%s" is not defined.', $orderIncrementIdField);
            Mage::throwException($message);
        }

        $order = Mage::getModel('sales/order')->loadByIncrementId($importData[$orderIncrementIdField]);
        if (!$order->getId()) {
            $message = Mage::helper('moogento_shipeasy')->__('Skipping import row, order "%s" not found.', $importData[$orderIncrementIdField]);
            Mage::throwException($message);
        }

        $shippingCostField = Mage::getStoreConfig('moogento_shipeasy/import/shipping_cost');
        $shippingCostField = ($shippingCostField) ? $shippingCostField : 'shipping_cost';

        $shippingCost = false;
        if (isset($importData[$shippingCostField]) && !empty($importData[$shippingCostField])) {
            $shippingCost = (float)$importData[$shippingCostField];
        }

        $trackingInfoField = Mage::getStoreConfig('moogento_shipeasy/import/tracking_info');
        $trackingInfoField = ($trackingInfoField) ? $trackingInfoField : 'tracking_info';

        $trackingInfo = array();
        if (isset($importData[$trackingInfoField]) && !empty($importData[$trackingInfoField])) {
            $trackingInfo = $importData[$trackingInfoField];
            if (strpos($trackingInfo, ',') !== false) {
                $trackingInfo = explode(',', $trackingInfo);
            } else {
                $trackingInfo = array($trackingInfo);
            }
        }

        $this->_shipOrder($order, $shippingCost, $trackingInfo);
    }
}