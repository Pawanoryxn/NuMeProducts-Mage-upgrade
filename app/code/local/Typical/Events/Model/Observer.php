<?php
class Typical_Events_Model_Observer
{
	
	public function shipStationUpdate(Varien_Event_Observer $observer)
    {
    		$cublicSize = 0;
		$order = $observer->getEvent()->getOrder();
		$items = $order->getAllItems();
		$orderIDRunning = $order->getId();
		$couponCodeUsed = $order->getcoupon_code();
		$itemcount=count($items);
		$shippingId = $order->getShippingAddress()->getId();
		$address = Mage::getModel('sales/order_address')->load($shippingId);
		$CountryDetails = $address->getcountry_id();
		if($CountryDetails != "US"):
		$TargetCoutry = $CountryDetails; 
		else:
		$TargetCoutry = "US";
		endif;
		foreach ($items as $itemId => $item)
		{
			 $itemID = $item->getproduct_id();
			 $itemQty = $item->getQtyOrdered();
			 $product = Mage::getModel('catalog/product')->load($itemID);
			 if($product->gettype_id() == "simple"):
			 $cublicSize = $cublicSize + ( $product->getcubic_inches() * $itemQty );
			 $update_price_column = $this->price_exclude_discount($itemID,$orderIDRunning,$couponCodeUsed);
			 endif;
		}
		$NewOrderDetails = Mage::getModel('sales/order')->load($orderIDRunning);
		$oldshippingDes = $NewOrderDetails->getshipping_description();
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$queryTable = "SELECT title,boxsize FROM shipstationboxsize WHERE cublicsize>='$cublicSize' ORDER BY cublicsize LIMIT 0,1";
		$results = $connection->fetchAll($queryTable);
		$boxSize = $results[0]['boxsize']; 
		$boxTitleSize = $results[0]['title']; 
		if($boxSize == "0" OR $boxSize == ""): 
		$newShpDetails = $boxTitleSize."( Custom Size )";
		else:
		$newShpDetails = $boxTitleSize."(".$boxSize.")";
		endif;
		if($newShpDetails != ""):
		$sqlqry = "UPDATE sales_flat_order set shipping_description='$newShpDetails' WHERE entity_id='$orderIDRunning'";
		$connection->query($sqlqry);
		endif;
	}

	public function price_exclude_discount($itemID,$orderIDRunning,$couponCodeUsed)
	{
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$queryData = "select base_price,discount_amount,parent_item_id from sales_flat_order_item where order_id='$orderIDRunning' AND product_id='$itemID'";
		$results = $connection->fetchAll($queryData);
		$parent_item_row_id = $results[0]['parent_item_id'];
		if($parent_item_row_id == ""):
		$priceNoDiscount = $results[0]['base_price'] - $results[0]['discount_amount'];
		$sqlqry = "UPDATE sales_flat_order_item set price_exclude_discount='$priceNoDiscount' WHERE order_id='$orderIDRunning' AND product_id='$itemID'";
		$connection->query($sqlqry);
		else:
		$queryDataConfig = "select base_price,discount_amount from sales_flat_order_item where order_id='$orderIDRunning' AND item_id='$parent_item_row_id'";
		$resultsConfig = $connection->fetchAll($queryDataConfig);
		$discountAmount = $resultsConfig[0]['discount_amount'];
		$priceNoDiscountConfig = $resultsConfig[0]['base_price'] - $discountAmount;
		$sqlqry = "UPDATE sales_flat_order_item set price_exclude_discount='$priceNoDiscountConfig' WHERE order_id='$orderIDRunning' AND product_id='$itemID'";
		$connection->query($sqlqry);
		endif;
	}
	
}
