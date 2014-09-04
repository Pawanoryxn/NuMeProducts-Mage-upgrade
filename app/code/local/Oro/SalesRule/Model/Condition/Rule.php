<?php
/**
 * @category   Oro
 * @package    Oro_SalesRule
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Nesting rules condition
 */
class Oro_SalesRule_Model_Condition_Rule extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * @var string
     */
    protected $_inputType = 'select';


    /**
     * Render chooser trigger
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        return '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="'
        . Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif')
        . '" alt="" class="v-middle rule-chooser-trigger" title="'
        . Mage::helper('rule')->__('Open Chooser') . '" /></a>';
    }

    /**
     * Chooser URL getter
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/nestingrule/chooserGrid', array(
            'value_element_id' => $this->_valueElement->getId(),
            'form' => $this->getJsFormObject(),
            'rule_id' => $this->getRule()->getId()
        ));
    }

    /**
     * Enable chooser selection button
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        return true;
    }

    /**
     * Render element HTML
     *
     * @return string
     */
    public function asHtml()
    {
        $this->_valueElement = $this->getValueElement();
        return $this->getTypeElementHtml()
        . Mage::helper('oro_salesrule')->__(
            'If Price Rule %s %s',
            $this->_valueElement->getHtml(),
            $this->getOperatorElementHtml()
        )
        . $this->getRemoveLinkHtml()
        . '<div class="rule-chooser" url="' . $this->getValueElementChooserUrl() . '"></div>';
    }

    /**
     * Specify allowed comparison operators
     *
     * @return Oro_SalesRule_Model_Condition_Rule
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();

        $this->setOperatorOption(array(
            '=='  => Mage::helper('oro_salesrule')->__('applied'),
            '!='  => Mage::helper('oro_salesrule')->__('not applied'),
        ));

        return $this;
    }

    /**
     * Validate nested rule id
     *
     * @param   Mage_Sales_Model_Quote_Address $object
     * @return  bool
     */
    public function validate(Varien_Object $object)
    {
        $ruleId = $this->getData('value');

        $isValid = Mage::getSingleton('salesrule/validator')->validateRule($ruleId, $object);

        return $this->getOperatorForValidate() == '!=' ? !$isValid : $isValid;
    }

} 
