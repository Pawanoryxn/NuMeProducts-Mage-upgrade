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
class Aitoc_Aitexporter_Model_Export_Type_Shipment_Comment implements Aitoc_Aitexporter_Model_Export_Type_Interface
{
    /**
     * 
     * @param SimpleXMLElement $shipmentXml
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param Varien_Object $exportConfig
     */
    public function prepareXml(SimpleXMLElement $shipmentXml, Mage_Core_Model_Abstract $shipment, Varien_Object $exportConfig)
    {
        /* @var $shipment Mage_Sales_Model_Order_Shipment */

        $shipmentCommentsXml = $shipmentXml->addChild('comments');

        foreach ($shipment->getCommentsCollection() as $shipmentComment)
        {
            $shipmentCommentXml = $shipmentCommentsXml->addChild('comment');

            foreach($shipmentComment->getData() as $field => $value)
            {
                $shipmentCommentXml->addChild($field, (string)$value);
            }
        }
    }
}