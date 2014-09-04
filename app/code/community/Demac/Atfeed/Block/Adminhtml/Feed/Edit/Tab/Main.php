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

/**
 * description
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Demac_Atfeed_Block_Adminhtml_Feed_Edit_Tab_Main
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
        return Mage::helper('atfeed')->__('Feed Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('atfeed')->__('Feed Information');
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
        $model = Mage::registry('current_atfeed_feed');

        $model->getData();

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('feed_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('atfeed')->__('General Information')));

//        if ($model->getId()) {
//            $fieldset->addField('id', 'hidden', array(
//                'name' => 'id',
//            ));
//        }

        $fieldset->addField('product_ids', 'hidden', array(
            'name' => 'product_ids',
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('atfeed')->__('Feed Name'),
            'title' => Mage::helper('atfeed')->__('Feed Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('atfeed')->__('Description'),
            'title' => Mage::helper('atfeed')->__('Description'),
            'style' => 'width: 90%; height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('atfeed')->__('Status'),
            'title'     => Mage::helper('atfeed')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('atfeed')->__('Active'),
                '0' => Mage::helper('atfeed')->__('Inactive'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }


        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_id', 'select', array(
                'name'      => 'website_id',
                'label'     => Mage::helper('atfeed')->__('Websites'),
                'title'     => Mage::helper('atfeed')->__('Websites'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
            ));
        }
        else {
            $fieldset->addField('website_id', 'hidden', array(
                'name'      => 'website_id',
                'value'     => Mage::app()->getStore(true)->getWebsiteId()
            ));
            $model->setWebsiteId(Mage::app()->getStore(true)->getWebsiteId());
        }

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        //$form->setUseContainer(true);

        $this->setForm($form);

        // field dependencies
//        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
//            ->addFieldMap($couponTypeFiled->getHtmlId(), $couponTypeFiled->getName())
//            ->addFieldMap($couponCodeFiled->getHtmlId(), $couponCodeFiled->getName())
//            ->addFieldMap($usesPerCouponFiled->getHtmlId(), $usesPerCouponFiled->getName())
//            ->addFieldDependence(
//                $couponCodeFiled->getName(),
//                $couponTypeFiled->getName(),
//                Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
//            ->addFieldDependence(
//                $usesPerCouponFiled->getName(),
//                $couponTypeFiled->getName(),
//                Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
//        );

        Mage::dispatchEvent('adminhtml_feed_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
