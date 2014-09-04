<?php
/**
 * i.e. app/code/local/Cross/Pricing/Model/Observer.php
 */
class Followup_Cartupdate_Model_Observer
{
	
	public function discountUpdate(Varien_Event_Observer $observer)
    {
    		/*Mage::getSingleton('core/session')->setspecialdiscountPer('10');
		  $discountPercentage  =  Mage::getSingleton('core/session')->getspecialdiscountPer();
		  $CartbaseuRL = Mage::getBaseUrl().'checkout/cart/?'.$discountPercentage;
		  $quote=$observer->getEvent()->getQuote();
		  
		  $myValue  =  Mage::getSingleton('core/session')->getspecialURLdiscount();
		  if($quote AND $myValue!='updated')
		  {
		  // find tax and shipping on quote
		  if($discountPercentage == 10)
		  $couponcode = 'NUME10';
		  elseif($discountPercentage == 20)
		  $couponcode = 'NUME20';
		  echo $couponcode;
		  Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($couponcode)->collectTotals()->save();
		  Mage::getSingleton('core/session')->setspecialURLdiscount('updated');
			header("Location:$CartbaseuRL");
			exit;
		  }elseif($quote AND $myValue=='updated')
		  {
		  if($discountPercentage == 10)
		  $couponcode = 'NUME10';
		  elseif($discountPercentage == 20)
		  $couponcode = 'NUME20';
		  echo $couponcode;
		  Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($couponcode)->collectTotals()->save();
			Mage::getSingleton('core/session')->setspecialURLdiscount('updated');
			}*/
			
 }
	
}
