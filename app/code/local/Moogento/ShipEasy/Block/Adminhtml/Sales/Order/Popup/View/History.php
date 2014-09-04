<?php

class Moogento_ShipEasy_Block_Adminhtml_Sales_Order_Popup_View_History
    extends Mage_Adminhtml_Block_Widget
{
    protected $_orderInstance = null;

    public function getOrderInstance()
    {
        if (is_null($this->_orderInstance)) {
            $this->_orderInstance = Mage::getModel('sales/order')->load($this->_getOrderId());
        }

        return $this->_orderInstance;
    }

    protected function _prepareLayout()
    {
        $onclick = "submitSzyCommentForm()";
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'   => Mage::helper('sales')->__('Submit Comment'),
                'class'   => 'save',
                'onclick' => $onclick,
            ));
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }

    public function getAdminComment()
    {
        $comment = '';
        if ($message = Mage::getStoreConfig('moogento_shipeasy/email_to/default_admin_message')) {
            $comment = Mage::helper('moogento_shipeasy/contact')->processMessage($message, $this->getOrderInstance());
        }
        return $comment;
    }

    public function getCustomerComment()
    {
        $comment = '';
        if ($message = Mage::getStoreConfig('moogento_shipeasy/email_to/default_customer_message')) {
            $comment = Mage::helper('moogento_shipeasy/contact')->processMessage($message, $this->getOrderInstance());
        }
        return $comment;
    }

    protected function _getOrderId()
    {
        return $this->getRequest()->getParam('order_id', 0);
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/post');
    }

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('moogento/sales/order/popup/comment.phtml');
    }
}