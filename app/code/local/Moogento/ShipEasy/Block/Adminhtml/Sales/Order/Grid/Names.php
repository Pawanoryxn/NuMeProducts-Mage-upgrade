<?php


class Moogento_ShipEasy_Block_Adminhtml_Sales_Order_Grid_Names
    extends Moogento_ShipEasy_Block_Adminhtml_Sales_Order_Grid_Skus
{
    protected $_xmlPathFillColor = 'moogento_shipeasy/grid/product_names_fill_color';
    protected $_xmlPathColorUnavailable = 'moogento_shipeasy/grid/product_names_fully_unavailable';
    protected $_xmlPathColorFullyAvailable = 'moogento_shipeasy/grid/product_names_fully_available';
    protected $_xmlPathColorPartiallyAvailable = 'moogento_shipeasy/grid/product_names_partially_available';

    protected $_xmlPathTruncateText = 'moogento_shipeasy/grid/product_names_truncate';
    protected $_xmlPathTruncateLength = 'moogento_shipeasy/grid/product_names_x_truncate';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('moogento/sales/order/grid/names.phtml');
    }
}