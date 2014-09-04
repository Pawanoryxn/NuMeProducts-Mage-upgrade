<?php
/**
 * @category   Oro
 * @package    Oro_Checkout
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Attirubte cart options tab class
 */
class Oro_Checkout_Block_Adminhtml_Attribute_Edit_Tab_Cart extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('oro/attribute/cart_options.phtml');
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('oro_checkout')->__('Cart labels');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('oro_checkout')->__('Cart labels');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $type = Mage::registry('entity_attribute')->getData('frontend_input');
        return ($type == 'select' || $type == 'multiselect');
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getOptionsCollection()
    {
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter(Mage::registry('entity_attribute')->getId())
            ->setPositionOrder('desc', true);
        Mage::getResourceModel('oro_checkout/attribute_option')->addCartLabelsInfo($optionCollection);
        $optionCollection->load();

        return $optionCollection;
    }

    /**
     * @return string
     */
    public function getAfter()
    {
        return 'labels';
    }
}