<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amacgregor
 * Date: 08/05/13
 * Time: 8:38 PM
 * To change this template use File | Settings | File Templates.
 */

class Demac_Atfeed_Block_Adminhtml_Feed_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_feed';
        $this->_blockGroup = 'atfeed';

        parent::__construct();

        $this->setFormActionUrl($this->getUrl('*/*/save'));

        $this->_updateButton('save', 'label', Mage::helper('atfeed')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('atfeed')->__('Delete Rule'));

        $rule = Mage::registry('current_atfeed_feed');

        if (!$rule->isDeleteable()) {
            $this->_removeButton('delete');
        }

        if ($rule->isReadonly()) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        } else {
            $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('atfeed')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save',
                'id'        => 'save_button'
            ), 10);
            $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'back/edit/') } ";

            $this->_addButton('save_and_generate', array(
                'label'     => Mage::helper('atfeed')->__('Save and Generate Feed'),
                'onclick'   => 'saveAndGenerate()',
                'class'     => 'save',
                'id'        => 'save_button'
            ), 10);
            $this->_formScripts[] = " function saveAndGenerate(){ editForm.submit($('edit_form').action + 'generate/edit/') } ";

        }

        #$this->setTemplate('promo/quote/edit.phtml');
    }
    /**
     * Get init JavaScript for form
     *
     * @return string
     */
    public function getFormInitScripts()
    {
        return $this->getLayout()->createBlock('core/template')
            ->setTemplate('demac/atfeed/feed/edit.phtml')
            ->toHtml();
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('current_atfeed_feed');
        if ($rule->getId()) {
            return Mage::helper('atfeed')->__("Edit Rule '%s'", $this->htmlEscape($rule->getName()));
        }
        else {
            return Mage::helper('atfeed')->__('New Feed');
        }
    }

    public function getProductsJson()
    {
        return '{}';
    }
}
