<?php

/**
 * Customer Segment grid
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 */
class Demac_Atfeed_Block_Adminhtml_Feed_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Intialize grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('feedGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }


    protected function _prepareCollection()
    {

//        $collection = new Varien_Data_Collection();
//
//        $feed = new Varien_Object();
//
//        $feed->setFeedId(1);
//        $feed->setName('test');
//        $feed->setFilename('test.csv');
//        $feed->setUrl('test.csv');
//        $feed->setStoreId(1);
//        $feed->setType('affiliate_traction');
//        $feed->setGeneratedAt('2013-05-08');
//        $feed->setUploadedAt('2013-05-08');
//        $feed->setIsActive(1);
//
//        $collection->addItem($feed);

        $collection = Mage::getModel('atfeed/feed')
            ->getResourceCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('atfeed')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('atfeed')->__('Feed Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

//        $this->addColumn('feed_type', array(
//            'header'    => Mage::helper('atfeed')->__('Feed Type'),
//            'align'     =>'right',
//            'width'     => '50px',
//            'index'     => 'type',
//        ));

        $this->addColumn('ftp_filename', array(
            'header'    => $this->__('Access Url'),
            'align'     =>  'left',
            'index'     => 'ftp_filename',
            'renderer'  => 'Demac_Atfeed_Block_Adminhtml_Feed_Grid_Renderer_AccessUrl'
        ));

        $this->addColumn('last_generated', array(
            'header'    =>  $this->__('Last Generated'),
            'align'     =>  'left',
            'index'     =>  'generated_at',
            'type'		=> 'datetime',
            'renderer'  => 'Demac_Atfeed_Block_Adminhtml_Feed_Grid_Renderer_Datetime'
        ));

        $this->addColumn('uploaded_at', array(
            'header'    => $this->__('Last Uploaded'),
            'align'     => 'left',
            'index'     => 'uploaded_at',
            'type'		=> 'datetime',
            'renderer'  => 'Demac_Atfeed_Block_Adminhtml_Feed_Grid_Renderer_Datetime'
        ));

        $this->addColumn('website_id', array(
            'header'    => Mage::helper('catalog')->__('Website'),
            'width'     => '100px',
            'sortable'  => false,
            'index'     => 'website_id',
            'type'      => 'options',
            'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'frame_callback' => array($this, 'decorateStatus'),
            'options'   => array(
                1 => 'Uploaded',
                0 => 'Error',
                2 => 'Running',
                3 => 'Warning'
            ),
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('atfeed')->__('Is Active?'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param Mage_Index_Model_Process $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case Demac_Atfeed_Model_Feed::STATUS_SUCCESSFUL :
                $class = 'grid-severity-notice';
                break;
            case Demac_Atfeed_Model_Feed::STATUS_WARNING :
                $class = 'grid-severity-major';
                break;
            case Demac_Atfeed_Model_Feed::STATUS_ERROR :
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }



//    /**
//     * Instantiate and prepare collection
//     *
//     * @return Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Grid
//     */
//    protected function _prepareCollection()
//    {
//
//        $dummyCollection = new Varien_Data_Collection();
//        $dummyShippingRule = new Varien_Object();
//
//        $dummyShippingRule->setRuleId('1');
//        $dummyShippingRule->setName('Test Rule');
//        $dummyShippingRule->setFrontendMethod('Standard Shipping (2-5 days)');
//        $dummyShippingRule->setCarrierMethod('Canada Post - Standard Post');
//
//        $dummyCollection->addItem($dummyShippingRule);
//
//        $this->setCollection($dummyCollection);
//
//
////        $collection = Mage::getModel('atfeed/shippingrule')->getCollection();
////        $this->setCollection($collection);
//        return parent::_prepareCollection();
//    }
//
//    /**
//     * Prepare columns for grid
//     *
//     * @return Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Grid
//     */
//    protected function _prepareColumns()
//    {
//        // this column is mandatory for the chooser mode. It needs to be first
//        $this->addColumn('grid_segment_id', array(
//            'header'    => Mage::helper('atfeed')->__('ID'),
//            'align'     =>'right',
//            'width'     => 50,
//            'index'     => 'rule_id',
//        ));
//
//        $this->addColumn('grid_shippingrule_name', array(
//            'header'    => Mage::helper('atfeed')->__('Shipping Rule Name'),
//            'align'     =>'left',
//            'index'     => 'name',
//        ));
//
//        $this->addColumn('grid_shippingrule_method', array(
//            'header'    => Mage::helper('atfeed')->__('Frontend Method'),
//            'align'     =>'left',
//            'index'     => 'frontend_method',
//        ));
//
//        $this->addColumn('grid_shippingrule_carrier', array(
//            'header'    => Mage::helper('atfeed')->__('Carrier Method'),
//            'align'     =>'left',
//            'index'     => 'carrier_method',
//        ));
//
//
//        $this->addColumn('grid_shippingrule_is_active', array(
//            'header'    => Mage::helper('atfeed')->__('Status'),
//            'align'     => 'left',
//            'width'     => 80,
//            'index'     => 'is_active',
//            'type'      => 'options',
//            'options'   => array(
//                1 => 'Active',
//                0 => 'Inactive',
//            ),
//        ));
//
//        return parent::_prepareColumns();
//    }

    /**
     * Return url for current row
     *
     * @param Enterprise_CustomerSegment_Model_Segment $row
     * @return string
     */
    public function getRowUrl($row)
    {
        if ($this->getIsChooserMode()) {
            return null;
        }
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Row click javasctipt callback getter
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        if ($this->getIsChooserMode() && $elementId = $this->getRequest()->getParam('value_element_id')) {
            return 'function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                if (trElement) {
                    $(\'' . $elementId . '\').value = trElement.down("td").innerHTML;
                    $(grid.containerId).up().hide();
                }}';
        }
        return 'openGridRow';
    }

    /**
     * Grid URL getter for ajax mode
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/shippingrule/grid', array('_current' => true));
    }
}
