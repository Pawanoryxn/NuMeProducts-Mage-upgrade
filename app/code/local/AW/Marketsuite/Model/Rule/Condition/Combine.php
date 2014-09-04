<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Marketsuite
 * @version    2.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Marketsuite_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('marketsuite/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $attributesOrder = array();
        $attributesOrder[] = array(
            'value' => 'marketsuite/rule_condition_order_numberorders',
            'label' => Mage::helper('salesrule')->__('Orders quantity'),
        );
        $attributesOrder[] = array(
            'value' => 'marketsuite/rule_condition_order_salesamount',
            'label' => Mage::helper('salesrule')->__('Sales amount'),
        );
        $attributesOrder[] = array(
            'value' => 'marketsuite/rule_condition_order_purchasedquantity',
            'label' => Mage::helper('salesrule')->__('Purchased quantity'),
        );

        $attributesCustomer = array();
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_customer_address|billing',
            'label' => Mage::helper('salesrule')->__('Billing address'),
        );
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_customer_address|shipping',
            'label' => Mage::helper('salesrule')->__('Shipping address'),
        );
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_customer_conditions|group_id',
            'label' => Mage::helper('salesrule')->__('Customer group'),
        );
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_customer_conditions|dob',
            'label' => Mage::helper('salesrule')->__('Date of birth'),
        );
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_customer_conditions|email',
            'label' => Mage::helper('salesrule')->__('Email'),
        );
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_customer_conditions|firstname',
            'label' => Mage::helper('salesrule')->__('First name'),
        );
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_customer_conditions|lastname',
            'label' => Mage::helper('salesrule')->__('Last name'),
        );
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_customer_conditions|gender',
            'label' => Mage::helper('salesrule')->__('Gender'),
        );
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_customer_conditions|newslettersubscription',
            'label' => Mage::helper('salesrule')->__('Newsletter subscription'),
        );
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_customer_conditions|annewslettersubscription',
            'label' => Mage::helper('salesrule')->__('Advanced newsletter subscription'),
        );
        $attributesCustomer[] = array(
            'value' => 'marketsuite/rule_condition_store_list',
            'label' => Mage::helper('salesrule')->__('Registered in store'),
        );

        $attributesShoppingCart = array();
        $attributesShoppingCart[] = array(
            'value' => 'marketsuite/rule_condition_shoppingcart_conditions|base_grand_total',
            'label' => Mage::helper('salesrule')->__('Grand total'),
        );
        $attributesShoppingCart[] = array(
            'value' => 'marketsuite/rule_condition_shoppingcart_conditions|items_count',
            'label' => Mage::helper('salesrule')->__('Number of Different Products'),
        );
        $attributesShoppingCart[] = array(
            'value' => 'marketsuite/rule_condition_shoppingcart_conditions|items_qty',
            'label' => Mage::helper('salesrule')->__('Total Items Quantity'),
        );
        $attributesShoppingCart[] = array(
            'value' => 'marketsuite/rule_condition_shoppingcart_conditions|base_subtotal',
            'label' => Mage::helper('salesrule')->__('Subtotal'),
        );

        $attributesProducts = array();
        $attributesProducts[] = array(
            'value' => 'marketsuite/rule_condition_product_productlist',
            'label' => Mage::helper('salesrule')->__('Product List'),
        );
        $attributesProducts[] = array(
            'value' => 'marketsuite/rule_condition_product_producthistory',
            'label' => Mage::helper('salesrule')->__('Product History'),
        );

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            array(
                 array(
                     'label' => Mage::helper('catalogrule')->__('Conditions Combination'),
                     'value' => 'marketsuite/rule_condition_combine',
                 ),
                 array(
                     'label' => Mage::helper('marketsuite')->__('Orders'),
                     'value' => $attributesOrder,
                 ),
                 array(
                     'label' => Mage::helper('marketsuite')->__('Customers'),
                     'value' => $attributesCustomer,
                 ),
                 array(
                     'label' => Mage::helper('marketsuite')->__('Shopping Cart'),
                     'value' => $attributesShoppingCart,
                 ),
                 array(
                     'label' => Mage::helper('marketsuite')->__('Products'),
                     'value' => $attributesProducts,
                 ),
            )
        );
        return $conditions;
    }

    public function getQuery()
    {
        $query = Mage::getModel('customer/customer')->getCollection();
        foreach ($this->getConditions() as $cond) {
            $query = $cond->getQuery($query);
        }
        return ((string)$query->getSelect());
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            if (
                $condition instanceof AW_Marketsuite_Model_Rule_Condition_Product_Productlist
                || $condition instanceof AW_Marketsuite_Model_Rule_Condition_Product_Producthistory
            ) {
                $condition->collectValidatedAttributes($productCollection);
            }
        }
        return $this;
    }
}