<?php
/**
 * Orders Export and Import
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitexporter
 * @version      1.2.5
 * @license:     Orqsb1o5IOBC2rn5itGJF1Fmsrvozo2C91UTuZiGeO
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitexporter_Model_Export_Type_Order_Payment_Transaction implements Aitoc_Aitexporter_Model_Export_Type_Interface
{
    /**
     * 
     * @param SimpleXMLElement $paymentXml
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param Varien_Object $exportConfig
     */
    public function prepareXml(SimpleXMLElement $paymentXml, Mage_Core_Model_Abstract $payment, Varien_Object $exportConfig)
    {
        /* @var $payment Mage_Sales_Model_Payment */

        if (!Mage::helper('aitexporter/version')->isPaymentTransactionsExist())
        {
            return false;
        }

        // Filter invoices by order Id
        $paymentTransactionCollection = Mage::getModel('sales/order_payment_transaction')
            ->getCollection()
            ->addAttributeToFilter('payment_id', $payment->getId())
            // ..
            ->load();

        $paymentTransactionsXml = $paymentXml->addChild('transactions');

        foreach ($paymentTransactionCollection as $paymentTransaction)
        {
            $paymentTransactionXml = $paymentTransactionsXml->addChild('transaction');
            foreach($paymentTransaction->getData() as $key=>$value)
            {
                if(is_array($value))
                {
                    $value = serialize($value);
                }
                $paymentTransactionXml->addChild($key, (string)$value);
            }
        }
    }
}