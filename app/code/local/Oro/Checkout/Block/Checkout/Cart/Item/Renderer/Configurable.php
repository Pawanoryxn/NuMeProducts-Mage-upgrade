<?php
/**
 * @category   Oro
 * @package    Oro_Checkout
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Added product configuration functionality on cart page
 */
class Oro_Checkout_Block_Checkout_Cart_Item_Renderer_Configurable extends Mage_Checkout_Block_Cart_Item_Renderer_Configurable
{

    /**
     * Get configure block html
     *
     * @return string
     */
    public function getConfigureBlockHtml()
    {
        $product = $this->getConfigurableProduct();
        Mage::helper('catalog/product')->prepareProductOptions($product, $this->getItem()->getBuyRequest());
        return $this->getLayout()->createBlock('oro_checkout/checkout_cart_item_configure')
            ->setProduct($product)->setItem($this->getItem())->toHtml();
    }

    /**
     * Check if item is a free promo
     *
     * @param $item
     * @return bool
     */
    public function isPromoItem(Mage_Sales_Model_Quote_Item $item)
    {
        return (bool) $item->getOptionByCode('ampromo_rule');
    }
}
