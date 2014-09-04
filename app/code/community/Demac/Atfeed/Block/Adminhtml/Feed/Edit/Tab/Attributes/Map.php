<?php

class Demac_Atfeed_Block_Adminhtml_Feed_Edit_Tab_Attributes_Map
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{

    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array(
        'custom_design',
        'custom_design_from',
        'custom_design_to',
        'custom_layout_update',
        'gift_message_available',
        'giftcard_amounts',
        'news_from_date',
        'news_to_date',
        'options_container',
        'price_view',
        'sku_type',
        'use_config_is_redeemable',
        'use_config_allow_message',
        'use_config_lifetime',
        'use_config_email_template',
        'tier_price',
        'minimal_price',
        'recurring_profile',
        'shipment_type'
    );

    /**
     * Default ignored attribute types
     *
     * @var array
     */
    protected $_ignoredAttributeTypes = array('hidden', 'media_image', 'image', 'gallery');


    public function __construct()
    {
        $this->setTemplate('demac/atfeed/feed/edit/attributes/map.phtml');
    }

    /**
     * Preparing global layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('atfeed')->__('Add New Attribute'),
                    'class' => 'add',
                    'id'    => 'add_new_attribute',
                    'on_click' => 'gContentAttribute.add()'
                ))
        );
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('atfeed')->__('Remove'),
                    'class' => 'delete delete-product-option',
                    'on_click' => 'gContentAttribute.remove(event)'
                ))
        );

        return parent::_prepareLayout();
    }

    /**
     * Get attributes select field id
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'gcontent_attribute';
    }

    /**
     * Get attributes select field name
     *
     * @return string
     */
    public function getFieldName ()
    {
        return 'attributes';
    }

    /**
     * Build HTML code for select element which contains all available Google's attributes
     *
     * @return string
     */
    public function getFeedAttributesSelectHtml()
    {
        $options[] = array('label' => $this->__('Custom attribute, no mapping'));

        $attributesTree = Mage::getSingleton('atfeed/config_attributes')
            ->getAttributes();

        foreach ($attributesTree as $destination => $attributes) {
            $options[] = array(
                'label' => $destination,
                'is_group' => true,
            );
            foreach ($attributes as $attribute => $params) {
                $options[$attribute] = array('label' => $params['name']);
                if ((int)$params['required'] == 1) {
                    $options[$attribute]['style'] = 'color: #940000;';
                }
            }
            $options[] = array(
                'is_group' => true,
                'is_close' => true
            );
        }

        $select = $this->getLayout()->createBlock('atfeed/adminhtml_feed_edit_tab_attributes_select')
            ->setId($this->getFieldId().'_{{index}}_feedattribute')
            ->setName($this->getFieldName().'[{{index}}][feed_attribute]')
            ->setOptions($options);

        return $this->_toOneLineString($select->toHtml());
    }

    /**
     * Build HTML select element of attribute set attributes
     *
     * @param boolean $escapeJsQuotes
     * @return string
     */
    public function getAttributesSelectHtml($escapeJsQuotes = false)
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setId($this->getFieldId().'_{{index}}_attribute')
            ->setName($this->getFieldName().'[{{index}}][attribute_id]')
            ->setOptions($this->_getAttributes($escapeJsQuotes));
        return $select->getHtml();
    }

    /**
     * Get HTML code for button "Add New Attribute"
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Get HTML code for button "Remove"
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Get attributes of an attribute set
     * Skip attributes not needed for Google Content
     *
     * @param int $setId
     * @param boolean $escapeJsQuotes
     * @return array
     */
    public function _getAttributes($escapeJsQuotes = false)
    {
        $attributes = $this->getAllowedAttributes();
        $result = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $result[$attribute->getAttributeId()] = $escapeJsQuotes
                ? $this->jsQuoteEscape($attribute->getFrontendLabel())
                : $attribute->getFrontendLabel();
        }
        return $result;
    }

    public function getAllowedAttributes()
    {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection');

        $titles = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if ($this->_isAllowedAttribute($attribute)) {
                $list[$attribute->getAttributeId()] = $attribute;
                $titles[$attribute->getAttributeId()] = $attribute->getFrontendLabel();
            }
        }
        asort($titles);
        $result = array();
        foreach ($titles as $attributeId => $label) {
            $result[$attributeId] = $list[$attributeId];
        }
        return $result;
    }

    /**
     * Check if attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute)
    {
        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
            && !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes)
            && $attribute->getFrontendLabel() != "";
    }

    /**
     * Encode the mixed $data into the JSON format
     *
     * @param mixed $data
     * @return string
     */
    protected function _toJson($data)
    {
        return Mage::helper('core')->jsonEncode($data);
    }

    /**
     * Format string to one line, cut symbols \n and \r
     *
     * @param string $string
     * @return string
     */
    protected function _toOneLineString($string)
    {
        return str_replace(array("\r\n", "\n", "\r"), "", $string);
    }

}
