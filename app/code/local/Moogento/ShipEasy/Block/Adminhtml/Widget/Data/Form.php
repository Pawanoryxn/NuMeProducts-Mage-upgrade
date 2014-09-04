<?php

class Moogento_ShipEasy_Block_Adminhtml_Widget_Data_Form extends Varien_Data_Form
{
    public function getHtmlAttributes()
    {
        return array('id', 'name', 'method', 'action', 'enctype', 'class', 'onsubmit', 'target');
    }
}