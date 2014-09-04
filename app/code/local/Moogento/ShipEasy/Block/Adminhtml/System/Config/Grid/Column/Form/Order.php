<?php

class Moogento_ShipEasy_Block_Adminhtml_System_Config_Grid_Column_Form_Order
    extends Mage_Adminhtml_Block_System_Config_Form
{

    protected function _getOrderStatutesInfo()
    {
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        $xml = '<config><groups><colors><fields>';
            $counter = 0;
            foreach($statuses as $statusCode => $statusLabel) {
                $counter++;
                $xml .= '<'.$statusCode.'>';
                $xml .= "<label>".$statusLabel."</label>";
                $xml .= '<frontend_type>text</frontend_type>';
                $xml .= '<sort_order>'.$counter.'</sort_order>';
                $xml .= "<show_in_default>1</show_in_default>";
                $xml .= "<show_in_website>0</show_in_website>";
                $xml .= "<show_in_store>0</show_in_store>";
                $xml .= '</'.$statusCode.'>';
            }
        $xml .= '</fields></colors></groups></config>';

        return $xml;
    }    

    protected function _getOrderGridXml()
    {
        $grid = Mage::app()->getLayout()->getBlock('sales_order.grid');
        $grid->toHtml();
        $columns = $grid->getColumns();

        $xml = '<config><groups><grid><fields>';

        $ordering = 0;
        foreach($columns as $key => $value) {
            if ($value->getHeader()) {
                $ordering++;

                        $xml .= "<{$key}_show>";
                            $xml .= "<label>{$value->getHeader()} column</label>";
                            $xml .= '<frontend_type>select</frontend_type>';
                            $xml .= '<source_model>moogento_shipeasy/adminhtml_system_config_source_grid_yesno</source_model>';
                            $xml .= '<sort_order>'.$ordering.'</sort_order>';
                            $xml .= "<show_in_default>1</show_in_default>";
                            $xml .= "<show_in_website>0</show_in_website>";
                            $xml .= "<show_in_store>0</show_in_store>";
                            $xml .= "<frontend_model>moogento_shipeasy/adminhtml_system_config_form_element_select</frontend_model>";
                        $xml .= "</{$key}_show>";

                        $ordering++;
                        $xml .= "<{$key}_order>";
                            $xml .= '<label>Order</label>';
                            $xml .= '<frontend_class>validate-number</frontend_class>';
                            $xml .= '<frontend_type>text</frontend_type>';
                            $xml .= '<sort_order>'.$ordering.'</sort_order>';
                            $xml .= "<show_in_default>1</show_in_default>";
                            $xml .= "<show_in_website>0</show_in_website>";
                            $xml .= "<show_in_store>0</show_in_store>";
                        $xml .= "</{$key}_order>";

                        /*
                         * Adding date format for created at attribute
                         */
                        if ($value->getId() == 'created_at') {
                            $ordering++;
                            $xml .= "<{$key}_format>";
                                $xml .= '<label>Date Format</label>';
                                $xml .= '<frontend_type>select</frontend_type>';
                                $xml .= '<source_model>moogento_shipeasy/adminhtml_system_config_source_grid_dateformat</source_model>';
                                $xml .= '<sort_order>'.$ordering.'</sort_order>';
                                $xml .= "<show_in_default>1</show_in_default>";
                                $xml .= "<show_in_website>0</show_in_website>";
                                $xml .= "<show_in_store>0</show_in_store>";
                            $xml .= "</{$key}_format>";
                        }

                        if ($value->getId() == 'admin_comments') {
                            $ordering++;
                            $xml .= "<{$key}_truncate>";
                                $xml .= '<label>Max Comment Length</label>';
                                $xml .= '<frontend_type>text</frontend_type>';
                                $xml .= '<sort_order>'.$ordering.'</sort_order>';
                                $xml .= "<show_in_default>1</show_in_default>";
                                $xml .= "<show_in_website>0</show_in_website>";
                                $xml .= "<show_in_store>0</show_in_store>";
                                $xml .= "<comment>Leave empty or set 0 to disable</comment>";
                            $xml .= "</{$key}_truncate>";
                        }

                        if (in_array($value->getId(), array('product_skus','product_names'))) {
                            $ordering++;
                            $xml .= "<{$key}_fill_color>";
                                $xml .= '<label>Fill cell with color accordingly to products availability</label>';
                                $xml .= '<frontend_type>select</frontend_type>';
                                $xml .= '<source_model>adminhtml/system_config_source_yesno</source_model>';
                                $xml .= '<sort_order>'.$ordering.'</sort_order>';
                                $xml .= "<show_in_default>1</show_in_default>";
                                $xml .= "<show_in_website>0</show_in_website>";
                                $xml .= "<show_in_store>0</show_in_store>";
                            $xml .= "</{$key}_fill_color>";

                            $ordering++;
                            $xml .= "<{$key}_truncate>";
                                $xml .= '<label>Truncate content</label>';
                                $xml .= '<frontend_type>select</frontend_type>';
                                $xml .= '<source_model>adminhtml/system_config_source_yesno</source_model>';
                                $xml .= '<sort_order>'.$ordering.'</sort_order>';
                                $xml .= "<show_in_default>1</show_in_default>";
                                $xml .= "<show_in_website>0</show_in_website>";
                                $xml .= "<show_in_store>0</show_in_store>";
                            $xml .= "</{$key}_truncate>";

                            $ordering++;
                            $xml .= "<{$key}_x_truncate>";
                                $xml .= '<label>Truncate if longer than</label>';
                                $xml .= '<frontend_type>text</frontend_type>';
                                $xml .= '<sort_order>'.$ordering.'</sort_order>';
                                $xml .= "<show_in_default>1</show_in_default>";
                                $xml .= "<show_in_website>0</show_in_website>";
                                $xml .= "<show_in_store>0</show_in_store>";
                            $xml .= "</{$key}_x_truncate>";
                        }


                        /**
                         * Adding Formatting for Purchased From/Website Column
                         */
                        if ($value->getId() == 'store_id') {
                            $ordering++;
                            $xml .= "<{$key}_format>";
                                $xml .= '<label>Field Format</label>';
                                $xml .= '<frontend_type>select</frontend_type>';
                                $xml .= '<source_model>moogento_shipeasy/adminhtml_system_config_source_grid_store</source_model>';
                                $xml .= '<sort_order>'.$ordering.'</sort_order>';
                                $xml .= "<show_in_default>1</show_in_default>";
                                $xml .= "<show_in_website>0</show_in_website>";
                                $xml .= "<show_in_store>0</show_in_store>";
                            $xml .= "</{$key}_format>";
                        }

                        if ($value->getId() == 'tracking_number') {
                            $ordering++;
                            $xml .= "<{$key}_base_link>";
                                $xml .= '<label>Tracking System Default Link</label>';
                                $xml .= '<frontend_type>text</frontend_type>';
                                $xml .= '<sort_order>'.$ordering.'</sort_order>';
                                $xml .= "<show_in_default>1</show_in_default>";
                                $xml .= "<show_in_website>0</show_in_website>";
                                $xml .= "<show_in_store>0</show_in_store>";
                            $xml .= "</{$key}_base_link>";
                        }
            }
        }

        $xml .= '</fields></grid></groups></config>';
        return $xml;
    }

    public function initForm()
    {
        $this->_initObjects();

        $form = new Varien_Data_Form();

        $sections = $this->_configFields->getSection($this->getSectionCode(), $this->getWebsiteCode(), $this->getStoreCode());

        $sections->extend(
            new Varien_Simplexml_Element($this->_getOrderGridXml())
        );

        $sections->extend(
            new Varien_Simplexml_Element($this->_getOrderStatutesInfo())
        );

        if (empty($sections)) {
            $sections = array();
        }
        foreach ($sections as $section) {
            /* @var $section Varien_Simplexml_Element */
            if (!$this->_canShowField($section)) {
                continue;
            }

            foreach ($section->groups as $groups){


                $groups = (array)$groups;
                usort($groups, array($this, '_sortForm'));

                foreach ($groups as $group){

                    /* @var $group Varien_Simplexml_Element */
                    if (!$this->_canShowField($group)) {
                        continue;
                    }

                    if ($group->frontend_model) {
                        $fieldsetRenderer = Mage::getBlockSingleton((string)$group->frontend_model);
                    } else {
                        $fieldsetRenderer = $this->_defaultFieldsetRenderer;
                    }

                    $fieldsetRenderer->setForm($this);
                    $fieldsetRenderer->setConfigData($this->_configData);
                    $fieldsetRenderer->setGroup($group);

                    if ($this->_configFields->hasChildren($group, $this->getWebsiteCode(), $this->getStoreCode())) {

                        $helperName = $this->_configFields->getAttributeModule($section, $group);

                        $fieldsetConfig = array('legend' => Mage::helper($helperName)->__((string)$group->label));
                        if (!empty($group->comment)) {
                            $fieldsetConfig['comment'] = (string)$group->comment;
                        }
                        if (!empty($group->expanded)) {
                            $fieldsetConfig['expanded'] = (bool)$group->expanded;
                        }

                        $fieldset = $form->addFieldset(
                            $section->getName() . '_' . $group->getName(), $fieldsetConfig)
                            ->setRenderer($fieldsetRenderer);
                        $this->_prepareFieldOriginalData($fieldset, $group);
                        $this->_addElementTypes($fieldset);

                        if ($group->clone_fields) {
                            if ($group->clone_model) {
                                $cloneModel = Mage::getModel((string)$group->clone_model);
                            } else {
                                Mage::throwException('Config form fieldset clone model required to be able to clone fields');
                            }
                            foreach ($cloneModel->getPrefixes() as $prefix) {
                                $this->initFields($fieldset, $group, $section, $prefix['field'], $prefix['label']);
                            }
                        } else {
                            $this->initFields($fieldset, $group, $section);
                        }

                        $this->_fieldsets[$group->getName()] = $fieldset;

                    }
                }
            }
        }

        $this->setForm($form);
        return $this;
    }    

}