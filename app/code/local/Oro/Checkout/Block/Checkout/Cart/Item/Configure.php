    <?php
/**
 * @category   Oro
 * @package    Oro_Checkout
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Added product configuration functionality on cart page
 */
class Oro_Checkout_Block_Checkout_Cart_Item_Configure extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('oro/checkout/cart/item/configure.phtml');
    }

    /**
     * Adding cart labels/colors to the config
     *
     * @return array
     */
    public function _getAdditionalConfig()
    {
        $config = array();
        $attributes = $this->getAllowAttributes();
        foreach ($attributes as $attribute) {
            $config += Mage::helper('oro_checkout')->getAttributeOptionCartColors(
                $attribute->getProductAttribute()->getId()
            );
        }

        return array('cart_config' => $config);
    }
}
