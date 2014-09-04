<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Demac_Atfeed_Block_Adminhtml_Feed_Edit_Tab_Attributes
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('atfeed')->__('Attribute Mapping');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('atfeed')->__('Attribute Mapping');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
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

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('feed_');

        $itemType = $this->getItemType();

        $fieldset = $form->addFieldset('attribute_fieldset', array('legend'=>Mage::helper('atfeed')->__('Attribute Mapping')));
//
//        $attributeSetsSelect = $this->getAttributeSetsSelectElement()
//            ->setValue($itemType->getAttributeSetId());
//        if ($itemType->getAttributeSetId()) {
//            $attributeSetsSelect->setDisabled(true);
//        }
//
//        $fieldset->addField('attribute_set', 'note', array(
//            'label'     => $this->__('Attribute Set'),
//            'title'     => $this->__('Attribute Set'),
//            'required'  => true,
//            'text'      => '<div id="attribute_set_select">' . $attributeSetsSelect->toHtml() . '</div>',
//        ));
//
//        $attributesBlock = $this->getLayout()
//            ->createBlock('atfeed/adminhtml_feed_edit_tab_attributes_map')
//            ->setTargetCountry('CA');

        $attributesBlock = $this->getLayout()->createBlock('atfeed/adminhtml_feed_edit_tab_attributes_map')
            ->setAttributeSetId(4)
            ->setTargetCountry('CA')
            ->setAttributeSetSelected(true);

//        if ($itemType->getId()) {
//            $attributesBlock->setAttributeSetId($itemType->getAttributeSetId())
//                ->setAttributeSetSelected(true);
//        }

        $attributes = Mage::registry('attributes');

        if (is_array($attributes) && count($attributes) > 0) {
            $attributesBlock->setAttributesData($attributes);
        }



        $fieldset->addField('attributes_box', 'note', array(
            'label'     => $this->__('Attributes Mapping'),
            'text'      => '<div id="attributes_details">' . $attributesBlock->toHtml() . '</div>',
        ));

        $form->addValues($itemType->getData());
//        $form->setUseContainer(true);
//        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getSaveUrl());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get Select field with list of available attribute sets for some target country
     *
     * @param  string $targetCountry
     * @return Varien_Data_Form_Element_Select
     */
    public function getAttributeSetsSelectElement()
    {
        $field = new Varien_Data_Form_Element_Multiselect();
        $field->setName('attribute_set_id')
            ->setId('multiselect_attribute_set')
            ->setForm(new Varien_Data_Form())
            ->addClass('required-entry')
            ->setValues($this->_getAttributeSetsArray());
        return $field;
    }


    /**
     * Get array with attribute setes which available for some target country
     *
     * @param  string $targetCountry
     * @return array
     */
    protected function _getAttributeSetsArray()
    {
        $entityType = Mage::getModel('catalog/product')->getResource()->getEntityType();
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityType->getId());

        foreach ($collection as $attributeSet) {
            $result[$attributeSet->getId()] = $attributeSet->getAttributeSetName();
        }
        return $result;
    }

    /**
     * Get current attribute set mapping from register
     *
     * @return Mage_GoogleShopping_Model_Type
     */
    public function getItemType()
    {
        return Mage::registry('current_atfeed_feed');
    }

    /**
     * Get URL for saving the current map
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('type_id' => $this->getItemType()->getId()));
    }
}
