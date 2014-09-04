<?php
/**
 * @category   Oro
 * @package    Oro_SalesRule
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

class Oro_SalesRule_Model_Quote_Discount extends Mage_SalesRule_Model_Quote_Discount
{

    /**
     * @var array
     */
    protected $_eventArgs;

    /**
     * Collect address discount amount
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_SalesRule_Model_Quote_Discount
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        Mage_Sales_Model_Quote_Address_Total_Abstract::collect($address);
        $quote = $address->getQuote();
        $store = Mage::app()->getStore($quote->getStoreId());
        $this->_calculator->reset($address);

        $this->_eventArgs = array(
            'website_id'        => $store->getWebsiteId(),
            'customer_group_id' => $quote->getCustomerGroupId(),
            'coupon_code'       => $quote->getCouponCode(),
        );

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
        $this->_calculator->initTotals($items, $address);

        $address->setDiscountDescription(array());

        foreach ($items as $item) {
            $item->setDiscountAmount(0);
            $item->setBaseDiscountAmount(0);
            $item->setDiscountPercent(0);
        }

        $rules = $this->_calculator->getRules();
        foreach ($rules as $rule) {
            $this->_calculateDiscount($items, $rule);
        }

        /**
         * process weee amount
         */
        if (Mage::helper('weee')->isEnabled() && Mage::helper('weee')->isDiscounted($store)) {
            $this->_calculator->processWeeeAmount($address, $items);
        }

        /**
         * Process shipping amount discount
         */
        $address->setShippingDiscountAmount(0);
        $address->setBaseShippingDiscountAmount(0);
        if ($address->getShippingAmount()) {
            $this->_calculator->processShippingAmount($address);
            $this->_addAmount(-$address->getShippingDiscountAmount());
            $this->_addBaseAmount(-$address->getBaseShippingDiscountAmount());
        }

        $this->_calculator->prepareDescription($address);
        return $this;
    }

    /**
     * @param array $items
     * @param null $rule
     */
    protected function _calculateDiscount($items, $rule)
    {
        $eventArgs = $this->_eventArgs;
        if ($rule) {
            /**
             * Save subtotal with discount for future use in condition validation
             */
            $this->_getAddress()->setBaseSubtotalWithDiscountOrigin($this->_getAddress()->getBaseSubtotalWithDiscount());
        }
        foreach ($items as $item) {
            if ($item->getNoDiscount()) {
                $item->setDiscountAmount(0);
                $item->setBaseDiscountAmount(0);
            }
            else {
                /**
                 * Child item discount we calculate for parent
                 */
                if ($item->getParentItemId()) {
                    continue;
                }

                $eventArgs['item'] = $item;
                Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $this->_process($child, $rule);

                        $eventArgs['item'] = $child;
                        Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

                        $this->_aggregateItemDiscount($child);
                    }
                } else {
                    $this->_process($item, $rule);
                    $this->_aggregateItemDiscount($item);
                }
            }
        }
    }

    /**
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param $rule
     */
    protected function _process($item, $rule)
    {
        $this->_addAmount($item->getDiscountAmount());
        $this->_addBaseAmount($item->getBaseDiscountAmount());
        $this->_calculator->processDiscountRule($item, $rule);
    }
}
