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
class Aitoc_Aitexporter_Model_Rewrite_Aitcheckoutfields extends Aitoc_Aitcheckoutfields_Model_Aitcheckoutfields
{
    public function saveCustomOrderData($iOrderId, $sPageType)
    {
        if (!$iOrderId OR !$sPageType) return false;

        if($steps = $this->getCheckoutAtrributeList(1, 1, $sPageType, true))
        {
            foreach($steps as $stepPlaceholders)
            {
                foreach($stepPlaceholders as $placeholderFields)
                {
                    foreach(array_keys($placeholderFields) as $fieldId)
                    {
                        $this->_saveData($this->_sCustomAttrTable, $iOrderId, $sPageType, $fieldId, false);
                    }
                }
            }
        }
        
        $order = Mage::getModel('sales/order')->load($iOrderId);
        Mage::dispatchEvent('aitcheckoutfields_aitexporter', array('order' => $order));
        
    }
}