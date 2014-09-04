<?php

class Moogento_ShipEasy_Model_Adminhtml_Sales_Order_Grid_Observer
{
    protected $_salesGridPage = false;

    public function triggerOrderGrid($event)
    {
        $this->_salesGridPage = true;
    }

    protected function _isAllowed($action)
    {
          
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/' . $action);
    }

    public function blockBeforeHtml($event)
    {

        $block = $event->getBlock();
        if ($block instanceof  Mage_Adminhtml_Block_Sales_Order) {
            $block->addButton(
                'import_shipments',
                array(
                    'label' => Mage::helper('moogento_shipeasy')->__('Import Shipments'),
                    'onclick' => 'setLocation(\'' . $block->getUrl('*/system_convert_shipments') .'\')',
                )
            );
            $block->updateButton('add', 'level', 1);
        } else if ($block instanceof  Mage_Adminhtml_Block_Sales_Order_Grid) {
            
            $block->getMassactionBlock()->addItem('sEzySeparator1', array(
             'label'=> Mage::helper('moogento_shipeasy')->__('---------------'),
             'url'  => '',
            ));
            
            $block->getMassactionBlock()->addItem('updateshippingcost_order', array(
                'label'=> 'sEzy '.Mage::helper('moogento_shipeasy')->__('Update Shipping Cost'),
                'url'  => $block->getUrl('*/sales_order_process/updateshippingcost'),
            ));

            $block->getMassactionBlock()->addItem('ship_order', array(
                'label'=> 'sEzy '.Mage::helper('moogento_shipeasy')->__('Ship'),
                'url'  => $block->getUrl('*/sales_order_process/massShip'),
            ));

            $block->getMassactionBlock()->addItem('invoice_order', array(
                'label'=> 'sEzy '.Mage::helper('moogento_shipeasy')->__('Invoice'),
                'url'  => $block->getUrl('*/sales_order_process/massInvoice'),
            ));

            $block->getMassactionBlock()->addItem('ship_invoice_order', array(
                'label'=> 'sEzy '.Mage::helper('moogento_shipeasy')->__('Ship & Invoice'),
                'url'  => $block->getUrl('*/sales_order_process/massProcess'),
            ));

            $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
          
            array_unshift($statuses, array('label'=>'', 'value'=>''));
            
            $block->getMassactionBlock()->addItem('order_change_status', array(
                'label'=> 'sEzy '.Mage::helper('moogento_shipeasy')->__('Change Status'),
                'url'  => $block->getUrl('*/sales_order_process/updateStatus'),
                'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
                )
            ));


            if ($skuColumn = $block->getColumn('product_skus')) {
                $skuColumn->setSortable(false);
            }
            if ($nameColumn = $block->getColumn('product_names')) {
                $nameColumn->setSortable(false);
            }

            if ($countryGroupColumn = $block->getColumn('country_region')) {
                $countryGroupColumn->setOptions(
                    Mage::getSingleton('moogento_shipeasy/adminhtml_system_config_source_country_group')->getCountryGroups()
                );
            }

            if ($this->_isAllowed('edit_shipping_cost')) {
                $currentValue = $block->getColumn('base_shipping_cost')->getFilter()->getValue();
                $dir = $block->getColumn('base_shipping_cost')->getDir();
                $block->addColumnAfter(
                    'base_shipping_cost',
                    array(
                        'header' => Mage::helper('moogento_shipeasy')->__('Shipping Cost'),
                        'index' => 'base_shipping_cost',
                        'type' => 'input',
                        'inline_css' => 'shipping_cost',
                        'renderer' => 'moogento_shipeasy/adminhtml_widget_grid_column_renderer_input_shippingcost',
                        'filter' => 'adminhtml/widget_grid_column_filter_price',
                        'display_currency_select' => false,
                        'currency' => 'order_currency_code',
                    ),
                    'base_profit'
                );

                $block->getColumn('base_shipping_cost')->getFilter()->setValue($currentValue);
                $block->getColumn('base_shipping_cost')->setDir($dir);
            }

            if (Mage::getStoreConfigFlag('moogento_shipeasy/grid/created_at_format') != 1) {

                $currentValue = $block->getColumn('created_at')->getFilter()->getValue();
                $dir = $block->getColumn('created_at')->getDir();

                $block->addColumn('created_at', array(
                    'header' => Mage::helper('moogento_shipeasy')->__('Date'),
                    'index' => 'created_at',
                    'type' => 'datetime',
                    'width' => '100px',
                    'format' => 'dd.MM.yy hh:mm'
                ));

                $block->getColumn('created_at')->getFilter()->setValue($currentValue);
                $block->getColumn('created_at')->setDir($dir);
            }

            /**
             * Purchased On Field
             */
            if (!Mage::app()->isSingleStoreMode()) {
                if (Mage::getStoreConfigFlag('moogento_shipeasy/grid/store_id_format') != 1) {
                    $currentValue = $block->getColumn('store_id')->getFilter()->getValue();
                    $dir = $block->getColumn('store_id')->getDir();
                    $block->addColumn('store_id', array(
                        'header'    => Mage::helper('sales')->__('Website'),
                        'index'     => 'store_id',
                        'type'      => 'store',
                        'store_view'=> true,
                        'display_deleted' => true,
                        'renderer' => 'moogento_shipeasy/adminhtml_widget_grid_column_renderer_store_simple',
                        'filter'   => 'moogento_shipeasy/adminhtml_widget_grid_column_filter_store_simple'
                    ));
                    $block->getColumn('store_id')->getFilter()->setValue($currentValue);
                    $block->getColumn('store_id')->setDir($dir);
                }
            }

            $columnsConfig = Mage::getStoreConfig('moogento_shipeasy/grid');
            $columnsArray = array();
            foreach($block->getColumns() as $column) {
                $columnId = $column->getId();
                $columnOrder = ($columnId == 'massaction') ? -1 : 0;
                if (!empty($columnsConfig[$columnId.'_order'])) {
                    $columnOrder = (int)$columnsConfig[$columnId.'_order'];
                }
                
                if($columnId != 'created_at' )
                {
                    $columnsArray[] = array(
						'position'  => $columnOrder,
						'column_id' => $columnId
					);
                }                
                
            }
            
             usort($columnsArray, array($this, '_sortingMethod'));


            foreach($columnsArray as $index => $columnInfo) {
                $columnId = $columnInfo['column_id'];
                if ($column = $block->getColumn($columnId)  ) {
                    $colValue = false;
                    if ($column->getFilter()) {
                        $colValue = $column->getFilter()->getValue();
                    }

                    $data = $column->getData();
                    $data['header'] = Mage::helper('moogento_shipeasy')->__($column->getHeader());
                    if ($columnId == 'status') {
                        $data['column_css_class'] = 'nowrap';
                    } else if ($columnId == 'base_shipping_cost') {
                        $data['filterable'] = false;
                        $data['filter'] = false;
                        $data['width'] = '80px';
                    } else if ($columnId == 'contact') {
                        $data['filter'] = false;
                        $data['sortable'] = false;
                    } else if ($columnId == 'admin_comments') {
                        $data['filter'] = false;
                        $data['sortable'] = false;
                    }

                    $block->addColumn($columnId, $data);
                    if ($colValue) {
                        $block->getColumn($columnId)->getFilter()->setValue($colValue);
                    }
                    if ($index) {
                        $block->addColumnsOrder($columnId, $columnsArray[$index-1]['column_id']);
                    }

                }
            }
            $block->sortColumnsByOrder();
            
        }
        return $this;
    }

    protected function _sortingMethod($col1, $col2)
    {
        if ($col1['position'] > $col2['position']) {
            return 1;
        } else {
            return -1;
        }
    }
}

