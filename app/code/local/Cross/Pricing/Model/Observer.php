<?php
/**
 * i.e. app/code/local/Cross/Pricing/Model/Observer.php
 */
class Cross_Pricing_Model_Observer
{
    public function cPrice(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $crosssell_prices = $product->getcrosssell_prices();
        $crosssell_status = $product->getcrosssell_status();
		$crossellpinf = Mage::app()->getRequest()->getParam('crosssell');
		if($crossellpinf == 1)
		{
		if($product->getTypeId() == 'simple')
		{
		$quote_item = $observer->getQuoteItem();
		if(is_numeric($crosssell_prices) AND $crosssell_status == '1')
            $customprice = $crosssell_prices;
			else
			$customprice = $product->getprice();
			$quote_item->setOriginalCustomPrice($customprice);
			$quote_item->setNoDiscount(true);
			$quote_item->save();
		}elseif($product->getTypeId() == 'configurable'){
		$quote_item = $observer->getEvent()->getQuoteItem();
			if(is_numeric($crosssell_prices) AND $crosssell_status == '1')
            $customprice = $crosssell_prices;
			else
			$customprice = $product->getprice();
			
			$quote_item->setOriginalCustomPrice($customprice);
			$quote_item->setNoDiscount(true);
			$quote_item->save();
			$quote_item->setTotalsCollectedFlag(false)->collectTotals();
		}
		}
    }
}