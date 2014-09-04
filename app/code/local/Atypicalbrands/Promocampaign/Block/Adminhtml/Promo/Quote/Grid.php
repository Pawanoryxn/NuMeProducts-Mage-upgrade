<?php

class Atypicalbrands_Promocampaign_Block_Adminhtml_Promo_Quote_Grid extends Mage_Adminhtml_Block_Promo_Quote_Grid {

    /**
     * Add grid columns
     *
     * @return Mage_Adminhtml_Block_Promo_Quote_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('rule_id', array(
            'header' => Mage::helper('salesrule')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('salesrule')->__('Rule Name'),
            'align' => 'left',
            'index' => 'name',
        ));

        $this->addColumn('createdats', array(
            'header' => Mage::helper('salesrule')->__('createdats'),
            'align' => 'left',
            'index' => 'createdats',
        ));

        $this->addColumn('publisher', array(
            'header' => Mage::helper('salesrule')->__('Publisher'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'publisher',
        ));
        $this->addColumn('campaign_id', array(
            'header' => Mage::helper('salesrule')->__('Campaign Id'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'campaign_id',
        ));
        $this->addColumn('campaign_type', array(
            'header' => Mage::helper('salesrule')->__('Campaign Type'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'campaign_type',
            'type' => 'options',
            'options' => array(
                '0' => '',
                'daily_deal' => Mage::helper('salesrule')->__('Daily Deal Campaigns'),
                'social_media' => Mage::helper('salesrule')->__('Social Media and Website Promotions'),
            ),
        ));

        $this->addColumn('campaign_start_date', array(
            'header' => Mage::helper('salesrule')->__('Campaign Start Date'),
            'align' => 'left',
            'width' => '120px',
            'type' => 'date',
            'index' => 'campaign_start_date',
        ));

        $this->addColumn('campaign_end_date', array(
            'header' => Mage::helper('salesrule')->__('Campaign End Date'),
            'align' => 'left',
            'width' => '120px',
            'type' => 'date',
            'index' => 'campaign_end_date',
        ));



        $this->addColumn('coupon_code', array(
            'header' => Mage::helper('salesrule')->__('Coupon Code'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'code',
        ));

        $this->addColumn('from_date', array(
            'header' => Mage::helper('salesrule')->__('Date Start'),
            'align' => 'left',
            'width' => '120px',
            'type' => 'date',
            'index' => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header' => Mage::helper('salesrule')->__('Date Expire'),
            'align' => 'left',
            'width' => '120px',
            'type' => 'date',
            'default' => '--',
            'index' => 'to_date',
        ));

        $this->addColumn('is_active', array(
            'header' => Mage::helper('salesrule')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'is_active',
            'type' => 'options',
            'options' => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('rule_website', array(
                'header' => Mage::helper('salesrule')->__('Website'),
                'align' => 'left',
                'index' => 'website_ids',
                'type' => 'options',
                'sortable' => false,
                'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
                'width' => 200,
            ));
        }

        $this->addColumn('sort_order', array(
            'header' => Mage::helper('salesrule')->__('Priority'),
            'align' => 'right',
            'index' => 'sort_order',
            'width' => 100,
        ));

        //parent::_prepareColumns();
        $this->sortColumnsByOrder();
        return $this;
    }

}
?>