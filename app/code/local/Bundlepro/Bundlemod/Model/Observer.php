<?php 
class Bundlepro_Bundlemod_Model_Observer extends Varien_Object
{
    public function salesQuoteItemSetCustomAttribute($observer)
    {
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();
        $quoteItem->setCustomAttribute($product->getCustomAttribute());
        return $this;
    }
}
?>