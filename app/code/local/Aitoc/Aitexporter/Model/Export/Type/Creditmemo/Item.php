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
class Aitoc_Aitexporter_Model_Export_Type_Creditmemo_Item implements Aitoc_Aitexporter_Model_Export_Type_Interface
{
    /**
     * 
     * @param SimpleXMLElement $creditmemoXml
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @param Varien_Object $exportConfig
     */
    public function prepareXml(SimpleXMLElement $creditMemoXml, Mage_Core_Model_Abstract $creditMemo, Varien_Object $exportConfig)
    {
        /* @var $creditMemo Mage_Sales_Model_Order_Creditmemo */

        $creditMemoItemsXml = $creditMemoXml->addChild('items');

        foreach ($creditMemo->getItemsCollection() as $creditMemoItem)
        {
            $creditMemoItemXml = $creditMemoItemsXml->addChild('item');
            
            foreach($creditMemoItem->getData() as $field => $value)
            {
                $creditMemoItemXml->addChild($field, (string)$value);
            }
        }
    }
}