<?php

class Moogento_ShipEasy_Helper_Contact extends Mage_Core_Helper_Abstract
{
    protected $_simpleProductTypes = array(
        'simple',
        'virtual',
        'downloadable'
    );

    protected function _isSimpleItem($item)
    {
        $result = false;

        if (
            in_array($item->getProductType(), $this->_simpleProductTypes) &&
            (
                !$item->getParentItem() ||
                ($item->getParentItem()->getProductType() == 'configurable')
            )
        ) {
            $result = true;
        }

        return $result;
    }

    protected function _isBundleProduct($item)
    {
        $result = false;

        if ($item->getProductType() == 'bundle') {
            $result = true;
        }
        return $result;
    }
    

    protected function _generateReplacements($order)
    {
        $replacements = array();

        /**
         * Customer Name
         */
        $replacements['{customer-name}'] = ($order->getData('shipping_name')) ?
            $order->getData('shipping_name') :
            $order->getData('billing_name');

        /*
         * Order Increment Id
         */
        $replacements['{order-id}'] = $order->getData('increment_id');

        /**
         * Order Skus & Names
         */
        $skus = array();
        $names = array();
        foreach($order->getItemsCollection() as $item) {
            if ($this->_isSimpleItem($item) || $this->_isBundleProduct($item)) {
                $skus[] = $item->getSku();
                $names[] = $item->getName();
            }
        }
        if (count($skus)) {
            $replacements['{ordered-skus}'] = implode("\r\n", $skus);
            $replacements['{ordered-product-names}'] = implode("\r\n", $names);
        }



        return $replacements;

    }

    protected function _processVars($text, $order)
    {
        $replacements = $this->_generateReplacements($order);
        foreach($replacements as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        return $text;
    }

    protected function _encodeEmailData($data)
    {
        return rawurlencode($data);
    }

    public function processMessage($message, $order)
    {
        return $this->_processVars(trim($message), $order);
    }

    public function processEmailSubject($subject, $order)
    {
        $subject = $this->_processVars($subject, $order);
        return $this->_encodeEmailData(trim($subject));
    }

    public function processEmailBody($body, $order)
    {
        $body = $this->_processVars($body, $order);
        return $this->_encodeEmailData(trim($body));
    }
}