<?php

class Moogento_ShipEasy_Adminhtml_Sales_Order_CommentController extends Mage_Adminhtml_Controller_Action
{
    public function formAction()
    {
        $this->_initLayoutMessages('adminhtml/session');
        $block = $this->getLayout()->createBlock('moogento_shipeasy/adminhtml_sales_order_popup_view_history');
        $this->getResponse()->appendBody(
            $block->toHtml()
        );
    }

    public function postAction()
    {
        $history = $this->getRequest()->getPost('history', array());
        $orderId = 0;
        if (count($history)) {
            $orderId = $history['order_id'];
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order && $order->getId()) {
                try {
                    $adminComment = $history['admin_comment'];
                    if ($adminComment) {
                        $order->addStatusHistoryComment($adminComment)
                            ->setIsVisibleOnFront(false)
                            ->setIsCustomerNotified(false);
                        $order->save();
                    }

                    $comment = $history['history_comment'];
                    if ($comment) {
                        $order->addStatusHistoryComment($comment)
                            ->setIsVisibleOnFront(true)
                            ->setIsCustomerNotified(true);

                        $comment = trim(strip_tags($comment));

                        $order->save();
                        $order->sendOrderUpdateEmail(true, $comment);
                    }
                    $this->_getSession()->addSuccess(Mage::helper('moogento_shipeasy')->__('Comment has been added to order'));
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('Can not add order comment'));
                }
            } else {
                $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('Can not load specific order'));
            }
        } else {
            $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('Can not find POST data to save order comment'));
        }
        $this->_redirect('*/*/form', array('order_id' => $orderId));
    }
}