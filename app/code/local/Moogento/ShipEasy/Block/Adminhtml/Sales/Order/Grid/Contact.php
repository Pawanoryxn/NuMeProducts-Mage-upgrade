<?php

class Moogento_ShipEasy_Block_Adminhtml_Sales_Order_Grid_Contact extends Mage_Adminhtml_Block_Template
{
    const XML_PATH_DEFAULT_EMAIL_SUBJECT = 'moogento_shipeasy/email_to/default_subject';
    const XML_PATH_DEFAULT_EMAIL_BODY = 'moogento_shipeasy/email_to/default_body';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('moogento/sales/order/grid/contact.phtml');
    }

    protected function _getCommentUrl()
    {
        return $this->getUrl('*/sales_order_comment/form', array('order_id' => $this->getOrder()->getId()));
    }

    protected function _getEmailBody()
    {
        $body = '';
        if ($body = Mage::getStoreConfig(self::XML_PATH_DEFAULT_EMAIL_BODY)) {
            return Mage::helper('moogento_shipeasy/contact')->processEmailBody($body, $this->getOrder());
        }

        return $body;
    }

    protected function _getEmailSubject()
    {
        $subject = '';
        if ($subject = Mage::getStoreConfig(self::XML_PATH_DEFAULT_EMAIL_SUBJECT)) {
            return Mage::helper('moogento_shipeasy/contact')->processEmailSubject($subject, $this->getOrder());
        }

        return $subject;
    }

    protected function _getMailToUrl()
    {
        $mailTo = $this->_getCustomerEmail();

        $defaultData = array();
        if ($subject = $this->_getEmailSubject()) {
            $defaultData[] = 'subject='.$subject;
        }

        if ($body = $this->_getEmailBody()) {
            $defaultData[] = 'body='.$body;
        }

        if (count($defaultData)) {
            $mailTo .= '?'. implode('&',$defaultData);
        }

        return $mailTo;

    }

    protected function _getCustomerEmail()
    {
        return Mage::getResourceSingleton('moogento_shipeasy/sales_order')
            ->getOrderColumnValue($this->getOrder(), 'customer_email');
    }
}