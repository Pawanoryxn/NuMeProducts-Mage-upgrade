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
class Aitoc_Aitexporter_Block_Export_Edit_Tab_Entities extends Aitoc_Aitexporter_Block_Export_Edit_Tab_Abstract
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        
        $this->setForm($form);

        $this->setTemplate('aitexporter/export_entities.phtml');

        return parent::_prepareForm();
    }
}