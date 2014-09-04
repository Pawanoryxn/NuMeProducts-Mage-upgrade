<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Xcoupon_Block_Adminhtml_Redeem extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        //create form structure
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $saleRulesIDData = $this->getRequest()->getParam('id');
        $promoData = Mage::getModel('salesrule/rule')->load($saleRulesIDData);
        $hlp = Mage::helper('amxcoupon');
        
        $fldSet = $form->addFieldset('amxcoupon_general', array('legend'=> $hlp->__('General')));
        $fldSet->addField('redeem_coupon_status', 'select', array(
          'label'     => $hlp->__('Redeem Coupons'),
          'name'      => 'redeem_coupon_status',
          'value' => $promoData['redeem_coupon_status'],
          'values'    => array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
            ))
        ));
                
        // $fldSet = $form->addFieldset('amxcoupon_generate', array('legend'=> $hlp->__('Generate Options')));
        $fldSet->addField('redeem_coupon_name', 'text', array(
          'label'     => $hlp->__('Name'),
          'name'      => 'redeem_coupon_name',
          'value'     => "$promoData[redeem_coupon_name]"
        ));               

        return parent::_prepareForm();
    }
}