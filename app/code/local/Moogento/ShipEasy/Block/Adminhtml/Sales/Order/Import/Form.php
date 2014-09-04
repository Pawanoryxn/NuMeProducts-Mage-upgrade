<?php

class Moogento_ShipEasy_Block_Adminhtml_Sales_Order_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('track_import_form');
        $this->setTitle(Mage::helper('moogento_shipeasy')->__('Tracks Import'));
    }

    protected function _prepareForm()
    {

        $form = new Moogento_ShipEasy_Block_Adminhtml_Widget_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('adminhtml/system_convert_shipments/post'),
                'method' => 'post',
                'enctype'   => 'multipart/form-data',
                'target' => '_blank'
            )
        );

        $fieldset = $form->addFieldset(
            'fieldset_main',
            array(
                'legend' => Mage::helper('moogento_shipeasy')->__('Data File Upload'),
                'class' => 'fieldset-wide'
            )
        );
        
        $fieldset->addField(
            'import_file',
            'file',
            array(
                'name' => 'import_file',
                'label' => Mage::helper('moogento_shipeasy')->__('Select an import file')
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
        
    }
}