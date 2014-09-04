<?php
/**
 * @category   Oro
 * @package    Oro_Checkout
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

require_once 'Atypicalbrands/Couponerror/controllers/Checkout/CartController.php';

/**
 * Shopping cart controller
 */
class Oro_Checkout_CartController extends Atypicalbrands_Couponerror_Checkout_CartController
{

    /**
     * Update customer's shopping cart
     */
    protected function _updateShoppingCart()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }
				
                $qqty = array();
                echo "<pre>";
                print_r($cartData);
                echo "</pre>";
                foreach($cartData as $value)
                foreach($value as $values)
                if(!is_array($values)) $qqty[] = $values;
               
                $i = 0;
                $currentsku='';
                $cart = Mage::getModel('checkout/cart');
                $qnt = 2;
                Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
                foreach($items as $item) {
                   
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $productType = $product->getTypeID();
                $item->setQty($qqty[$i]);
                echo $qqty[$i];
                if($productType == 'simple')
                $i = $i + 1;
               
                $currentsku = $item->getSku();
                //echo '<br />';
                }
                    $cart->save();
				
				
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $data['qty'] = $filter->filter(trim($data['qty']));
                        $cartData[$index]['qty'] = $data['qty'];
                    }

                    $quoteItem = $cart->getQuote()->getItemById($index);
                    // cart coloring issue starts

                    // cart coloring issue ends

                    // cart coloring issue ends

                    if (!$quoteItem) {
                        Mage::throwException($this->__('Quote item is not found.'));
                    }
                    $ruleOption = $quoteItem->getProduct()->getCustomOption('ampromo_rule');
                    if ($ruleOption) {
                        /**
                         * If it's a free promo product, saving changed state to session for future use.
                         * No need to save cart because promo items are added to the cart dynamically
                         */
                        $ampromoRuleId = $ruleOption->getValue();
                        $sku = Mage::getModel('catalog/product')->load($quoteItem->getProduct()->getId())->getSku();
                        $this->_getSession()->setData('ampromo_' . $ampromoRuleId . '_' . $sku, $data);
                    } else {
                        if ($quoteItem->getProductType() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
                            $item = $cart->updateItem($index, new Varien_Object($data));
                            if (is_string($item)) {
                                Mage::throwException($item);
                            }
                            if ($item->getHasError()) {
                                Mage::throwException($item->getMessage());
                            }
                        }
                    }
                }
                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)
                    ->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }
}
