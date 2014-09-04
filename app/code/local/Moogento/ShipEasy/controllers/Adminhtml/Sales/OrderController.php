<?php

require_once("Mage/Adminhtml/controllers/Sales/OrderController.php");

class Moogento_ShipEasy_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    

    public function massCancelAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countCancelOrder = 0;
        $countNonCancelOrder = 0;
        foreach ($orderIds as $orderId) {
            try {
                $order = Mage::getModel('sales/order')->load($orderId);
                if ($order->getId()) {
                    $order->setState(
                        Mage_Sales_Model_Order::STATE_CANCELED,
                        true,
                        ''
                    );
                    $order->save();
                    $countCancelOrder++;
                } else {
                    $countNonCancelOrder++;
                }

            } catch (Exception $e) {
                $countNonCancelOrder++;
            }
        }
        if ($countNonCancelOrder) {
            if ($countCancelOrder) {
                $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('%s order(s) cannot be canceled', $countNonCancelOrder));
            } else {
                $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('The order(s) cannot be canceled'));
            }
        }
        if ($countCancelOrder) {
            $this->_getSession()->addSuccess(Mage::helper('moogento_shipeasy')->__('%s order(s) have been canceled.', $countCancelOrder));
        }
        $this->_redirect('*/*/');
    }
}