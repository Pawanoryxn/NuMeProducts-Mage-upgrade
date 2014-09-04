<?php

class Moogento_ShipEasy_Adminhtml_Sales_Order_ProcessController extends Mage_Adminhtml_Controller_Action
{
    public function updateStatusAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('status')) {
            $orderIds = $this->getRequest()->getPost('order_ids', array());
            $countSuccess = 0;
            $countFail = 0;
            $status = $this->getRequest()->getPost('status');
            foreach($orderIds as $id) {
                try{
                    $order = Mage::getModel('sales/order')->load($id);
                    $order->setStatus($status);
                    $order->save();
                    $countSuccess++;
                } catch(Exception $e) {
                    $countFail++;
                }
            }
            if ($countFail) {
                if ($countSuccess) {
                    $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('%s order(s) statuses cannot be updated', $countFail));
                } else {
                    $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('The order(s) statuses cannot be updated'));
                }
            }
            if ($countSuccess) {
                $this->_getSession()->addSuccess(Mage::helper('moogento_shipeasy')->__('%s order(s) statuses have been updated.', $countSuccess));
            }
            
        }
        $this->_redirect('*/sales_order/');
    }

    public function updateshippingcostAction()
    {
        if ($this->getRequest()->isPost()) {
            $shippingCostInfo = $this->getRequest()->getPost('base_shipping_cost', false);
            if (array($shippingCostInfo) && count($shippingCostInfo)) {
                $countSuccess = 0;
                $countFail = 0;
                foreach ($shippingCostInfo as $orderId => $shippingCost) {

                    if (empty($shippingCost)) {
                        continue;
                    }

                    $order = Mage::getModel('sales/order')->load($orderId);
                    if ($order && $order->getId()) {
                        try{
                            $order->setBaseShippingCost(($shippingCost) ? $shippingCost : 0);
                            $order->save();
                            $countSuccess++;
                        } catch(Exception $e) {
                            $countFail++;
                            Mage::logException($e);
                        }
                    } else {
                        $countFail++;
                    }
                }
                if ($countFail) {
                    if ($countSuccess) {
                        $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('%s order(s) cannot be updated', $countFail));
                    } else {
                        $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('The order(s) cannot be updated'));
                    }
                }
                if ($countSuccess) {
                    $this->_getSession()->addSuccess(Mage::helper('moogento_shipeasy')->__('%s order(s) have been updated.', $countSuccess));
                }
            }
        }
        $this->_redirect('*/sales_order/');
    }

    public function massInvoiceAction()
    {
        if ($this->getRequest()->isPost()) {
            $orderIds = $this->getRequest()->getPost('order_ids', array());
            $countSuccess = 0;
            $countFail = 0;
            foreach($orderIds as $id) {
                try {
                    if ($invoice = Mage::helper('moogento_shipeasy/sales')->initInvoice($id)) {
                        $invoice->register();
                        $invoice->setEmailSent(true);
                        $invoice->getOrder()->setCustomerNoteNotify(true);
                        $invoice->getOrder()->setIsInProcess(true);
                        $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder());
                        $transactionSave->save();
                        try {
                            $invoice->sendEmail(true, '');
                        } catch (Exception $e) {
                            Mage::logException($e);
                        }
                        $countSuccess++;
                    } else {
                        $countFail++;
                    }
                } catch (Exception $ex) {
                    $countFail++;
                }
            }

            if ($countFail) {
                if ($countSuccess) {
                    $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('%s order(s) cannot be invoiced', $countFail));
                } else {
                    $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('The order(s) cannot be invoiced'));
                }
            }
            if ($countSuccess) {
                $this->_getSession()->addSuccess(Mage::helper('moogento_shipeasy')->__('%s order(s) have been invoiced.', $countSuccess));
            }
        }
        $this->_redirect('*/sales_order/');
    }

    public function massShipAction()
    {
        if ($this->getRequest()->isPost()) {
            $shippingCostInfo = $this->getRequest()->getPost('base_shipping_cost', false);
            $orderIds = $this->getRequest()->getPost('order_ids', array());
            $trackingNo = $this->getRequest()->getPost('tracking_number', array());

            $countSuccess = 0;
            $countFail = 0;
            foreach($orderIds as $id) {
                try {
                    if ($shipment = Mage::helper('moogento_shipeasy/sales')->initShipment($id)) {
                        $shipment->register();
                        $shipment->setEmailSent(true);
                        $shipment->getOrder()->setCustomerNoteNotify(true);

                        if (isset($shippingCostInfo[$id])) {
                            if (!empty($shippingCostInfo[$id])) {
                                $shipment->getOrder()->setBaseShippingCost(
                                    $shippingCostInfo[$id]
                                );
                            }
                        }

                        if (isset($trackingNo[$id])) {
                            if (trim($trackingNo[$id])) {
                                $track = Mage::helper('moogento_shipeasy/track')->getTrackModel(
                                    trim($trackingNo[$id])
                                );
                                $shipment->addTrack($track);
                            }
                        }

                        Mage::helper('moogento_shipeasy/sales')->saveShipment($shipment, 'shipped');
                        $shipment->sendEmail(true, '');
                        $countSuccess++;
                    } else {
                        $countFail++;
                    }
                } catch (Exception $ex) {
                    $countFail++;
                }
            }

            if ($countFail) {
                if ($countSuccess) {
                    $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('%s order(s) cannot be shipped', $countFail));
                } else {
                    $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('The order(s) cannot be shipped'));
                }
            }
            if ($countSuccess) {
                $this->_getSession()->addSuccess(Mage::helper('moogento_shipeasy')->__('%s order(s) have been shipped.', $countSuccess));
            }
        }
        $this->_redirect('*/sales_order/');
    }

    public function massProcessAction()
    {
        if ($this->getRequest()->isPost()) {
            $orderIds = $this->getRequest()->getPost('order_ids', array());
            $shippingCostInfo = $this->getRequest()->getPost('base_shipping_cost', false);
            $trackingNo = $this->getRequest()->getPost('tracking_number', array());
            $countSuccess = 0;
            $countFail = 0;

            foreach($orderIds as $id) {
                try {
                    if ($invoice = Mage::helper('moogento_shipeasy/sales')->initInvoice($id)) {
                        $invoice->register();
                        $invoice->setEmailSent(true);
                        $invoice->getOrder()->setCustomerNoteNotify(true);
                        $invoice->getOrder()->setIsInProcess(true);

                        if (isset($shippingCostInfo[$id])) {
                            if (!empty($shippingCostInfo[$id])) {
                                $invoice->getOrder()->setBaseShippingCost(
                                    $shippingCostInfo[$id]
                                );
                            }
                        }

                        $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder());

                        $shipment = Mage::helper('moogento_shipeasy/sales')->prepareShipment($invoice);
                        if ($shipment) {

                            if (isset($trackingNo[$id])) {
                                if (trim($trackingNo[$id])) {
                                    $track = Mage::helper('moogento_shipeasy/track')->getTrackModel(
                                        trim($trackingNo[$id])
                                    );
                                    $shipment->addTrack($track);
                                }
                            }

                            $shipment->setEmailSent(true);
                            $transactionSave->addObject($shipment);
                        }
                        $transactionSave->save();
                        try {
                            $invoice->sendEmail(true, '');
                        } catch (Exception $e) {
                            Mage::logException($e);
                        }
                        if ($shipment) {
                            try {
                                $shipment->sendEmail(true, '');
                            } catch (Exception $e) {
                                Mage::logException($e);
                            }
                        }
                        $countSuccess++;
                    } else if ($shipment = Mage::helper('moogento_shipeasy/sales')->initShipment($id)) {
                        $shipment->register();
                        $shipment->setEmailSent(true);
                        $shipment->getOrder()->setCustomerNoteNotify(true);

                        if (isset($shippingCostInfo[$id])) {
                            if (!empty($shippingCostInfo[$id])) {
                                $shipment->getOrder()->setBaseShippingCost(
                                    $shippingCostInfo[$id]
                                );
                            }
                        }

                        if (isset($trackingNo[$id])) {
                            if (trim($trackingNo[$id])) {
                                $track = Mage::helper('moogento_shipeasy/track')->getTrackModel(
                                    trim($trackingNo[$id])
                                );
                                $shipment->addTrack($track);
                            }
                        }

                        Mage::helper('moogento_shipeasy/sales')->saveShipment($shipment, 'shipped');
                        $shipment->sendEmail(true, '');
                        $countSuccess++;

                    } else {
                        $countFail++;
                    }
                } catch (Exception $ex) {
                    $countFail++;
                }
            }

            if ($countFail) {
                if ($countSuccess) {
                    $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('%s order(s) cannot be processed', $countFail));
                } else {
                    $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('The order(s) cannot be processed'));
                }
            }
            if ($countSuccess) {
                $this->_getSession()->addSuccess(Mage::helper('moogento_shipeasy')->__('%s order(s) have been processed.', $countSuccess));
            }

        }
        $this->_redirect('*/sales_order/');
    }
}