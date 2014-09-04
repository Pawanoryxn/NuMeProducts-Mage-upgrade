<?php
/**
 * @category   Oro
 * @package    Oro_SalesRule
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Chooser sales rule grid class
 */
class Oro_SalesRule_Block_Adminhtml_Rule_Grid_Chooser extends Mage_Adminhtml_Block_Promo_Quote_Grid
{

    /**
     * Intialize grid
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('nestingrule_grid_chooser_'.$this->getId());
        }

        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);

        $form = $this->getRequest()->getParam('form');
        if ($form) {
            $this->setRowClickCallback("$form.chooserGridRowClick.bind($form)");
            $this->setCheckboxCheckCallback("$form.chooserGridRadioCheck.bind($form)");
            $this->setRowInitCallback("$form.chooserGridRowInit.bind($form)");
        }
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Promo_Quote_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_SalesRule_Model_Mysql4_Rule_Collection */
        $collection = Mage::getModel('salesrule/rule')
            ->getResourceCollection();
        $collection->addWebsitesToResult();

        $editRuleId = $this->getRequest()->getParam('rule_id');
        if ($editRuleId) {
            $collection->addFieldToFilter('rule_id', array('neq' => $editRuleId));
        }

        $this->setCollection($collection);

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        return $this;
    }

    /**
     * Row click javascript callback getter
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        return $this->_getData('row_click_callback');
    }

    /**
     * Prepare columns for grid
     *
     * @return Oro_SalesRule_Block_Adminhtml_Rule_Grid_Chooser
     */
    protected function _prepareColumns()
    {
        $this->addColumn('nesting_rule_id', array(
            'header_css_class' => 'a-center',
            'type'      => 'radio',
            'name'      => 'nesting_rule_id',
            'html_name' => 'nesting_rule_id',
            'values'    => $this->_getSelectedRule(),
            'align'     => 'center',
            'index'     => 'rule_id',
            'use_index' => true,
        ));

        parent::_prepareColumns();

        return $this;
    }

    /**
     * Get selected Rule
     *
     * @return array
     */
    protected function _getSelectedRule()
    {
        $ruleId = $this->getRequest()->getPost('selected', array());

        return $ruleId;
    }

    /**
     * Grid URL getter for ajax mode
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/nestingrule/chooserGrid', array('_current' => true));
    }
}
