<?php

class Demac_Atfeed_Block_Adminhtml_Feed extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize invitation manage page
     *
     * @return \Demac_Atfeed_Block_Adminhtml_Feed
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_feed';
        $this->_blockGroup = 'atfeed';
        $this->_headerText = Mage::helper('atfeed')->__('Manage Product Feeds');
        $this->_addButtonLabel = Mage::helper('atfeed')->__('Add a Product Feed');
        parent::__construct();
    }

}
