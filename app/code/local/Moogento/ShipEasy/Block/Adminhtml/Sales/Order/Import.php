<?php

class Moogento_ShipEasy_Block_Adminhtml_Sales_Order_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_headerText = Mage::helper('moogento_shipeasy')->__('Shipping Tracks Import');
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->_updateButton('save', 'label', Mage::helper('moogento_shipeasy')->__('Run Import'));
        $this->_updateButton('save', 'on_click', 'editForm.submit(); $(\'import_file\').setValue(\'\');');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'form',
            $this->getLayout()->createBlock(
                'moogento_shipeasy/adminhtml_sales_order_import_form'
            )
        );
        return parent::_prepareLayout();
    }
}