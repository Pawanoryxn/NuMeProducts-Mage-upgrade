<?php
class Bworld_Postcode_IndexController extends Mage_Core_Controller_Front_Action{
    
	public function IndexAction() {
		$session = Mage::getSingleton('checkout/session');
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$quoteid = $quote->getId(); 
		if($quoteid) {                    
			try{
				$billAddress = $quote->getBillingAddress();
				$address = $billAddress->getData();
			
				$address1 = $address['street'];
				$firm_name = $address['company'];
				$zip = $address['postcode'];
				$country_id = $address['country_id'];
				
				$country_ship = "US";
				if($quote->getShippingAddress()){
					$shipAddress = $quote->getShippingAddress();
					$shipping = $shipAddress->getData();
					$country_ship = $shipping['country_id'];
				}

				if($country_id != "US"){
					$pa = new CapturePlus_Interactive_Find_v2_00 ("XA37-BD49-UF22-ZN66", $address1, "", "Everything", $country_id, "EN");
					$pa->MakeRequest();
					if ($pa->HasData())
					{
					    //result to be alert on Dialog box
						$data = $pa->HasData();
						//print_r($data);

						$received_address = (string) $data[0]["Text"][0];
						if($data[0]["Text"] == "Invalid Addess"){
							$received_address = "Invalid Addess";
						}
						
						$entered_address = $firm_name.', '.$address1.', '.$zip;
						$str_1 = trim(strtolower($entered_address));
						$str_2 = trim(strtolower($received_address));

						similar_text($str_1, $str_2, $percentage);

						$formated_percents = number_format($percentage);
						$final_billing = "Empty";
						if($formated_percents <= 90){
							$final_billing = $received_address;
							
						}
						else{
							$final_billing = "National";
						}
					}
				}
				else{
					$final_billing = "National";
				}
				if($country_ship != "US"){

					$address1 = $shipping['street'];
					$firm_name = $shipping['company'];
					$zip = $shipping['postcode'];

					$pa = new CapturePlus_Interactive_Find_v2_00 ("XA37-BD49-UF22-ZN66", $address1, "", "Everything", $country_ship, "EN");
					$pa->MakeRequest();
					if ($pa->HasData())
					{
					    //result to be alert on Dialog box
						$data = $pa->HasData();
						
						$received_address = (string) $data[0]["Text"][0];
						if($data[0]["Text"] == "Invalid Addess"){
							$received_address = "Invalid Addess";
						}
						$entered_address = $firm_name.', '.$address1.', '.$zip;
						$str_1 = trim(strtolower($entered_address));
						$str_2 = trim(strtolower($received_address));
						similar_text($str_1, $str_2, $percentage);

						$formated_percents = number_format($percentage);
						$final_shiping = "Empty";
						if($formated_percents <= 90){
							$final_shiping = $received_address;
						}else{
							$final_shiping = "National";
						}
					}
				}else{
					$final_shiping = "National";
				}
				if($country_ship != "US" || $country_id != "US"){
					$json_data = array('Billing'=>$final_billing, 'Shipping'=>$final_shiping);
					$data = json_encode((array)$json_data);
					header('Content-Type: application/json');
					echo ($data);				
				}
			} catch (Exception $e) {            
				Mage::logException($e);
				$response['error'] = $e->getMessage();
			}
		}
    }
	
	public function Popup_BillAction() {

		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$quoteid = $quote->getId();
		if($quoteid){                    
			if($quote->getBillingAddress()){
				$billAddress = $quote->getBillingAddress();
				$address = $billAddress->getData();
				//echo '<pre>';
				//print_r($address);
			}
		}
		
		$_custom_address = array (
			'address_id' => $shipping['address_id'],
			'firstname' => $this->getRequest()->getParam('firstname'),
			'lastname' => $this->getRequest()->getParam('lastname'),
			'company' => $this->getRequest()->getParam('company'),
			'email'	=> $this->getRequest()->getParam('email'),
			'street' => array (
					'0' => $this->getRequest()->getParam('street1'),
					'1' => $this->getRequest()->getParam('street2'),
				),
			'city' => $this->getRequest()->getParam('city'),
			'region_id' => $this->getRequest()->getParam('region_id'),
			'region' => $this->getRequest()->getParam('region'),
			'postcode' => $this->getRequest()->getParam('postcode'),
			'country_id' => $this->getRequest()->getParam('country_id'),
			'telephone' => $this->getRequest()->getParam('telephone'),
			'save_in_address_book' => '1',
		);

		$data = $this->getRequest()->getPost('billing', $_custom_address);
		$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

		if (isset($data['email'])) {
			$data['email'] = trim($data['email']);
		}
		$getOnePageObj = Mage::getSingleton('checkout/type_onepage');
		try {
			$result = $getOnePageObj->saveBilling($data, $customerAddressId);
			if(empty($result)){
				$result = 'saved';
			}
		}
		catch (Exception $ex) {
			Zend_Debug::dump($ex->getMessage());
		}
		
		//$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId());

		$customAddress = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
		
		//Mage::getSingleton('checkout/session')->getQuote()->setBillingAddress(Mage::getSingleton('sales/quote_address')->importCustomerAddress($customAddress));
		
		/*
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$quoteid = $quote->getId();
		if($quoteid){                    
			if($quote->getBillingAddress()){
				$billAddress = $quote->getBillingAddress();
				$address = $billAddress->getData();
				echo '<pre>';
				print_r($address);
			}
		}
		*/
		$data = json_encode((array)$result);
		header('Content-Type: application/json');
		echo ($data);
	}

	public function Popup_ShipAction() {

		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$quoteid = $quote->getId();
		if($quoteid){                    
			if($quote->getBillingAddress()){
				$billAddress = $quote->getBillingAddress();
				$address = $billAddress->getData();
				//echo '<pre>';
				//print_r($address);
			}
		}
		

		$_custom_address = array (
			'address_id' => $shipping['address_id'],
			'firstname' => $this->getRequest()->getParam('firstname'),
			'lastname' => $this->getRequest()->getParam('lastname'),
			'company' => $this->getRequest()->getParam('company'),
			'street' => array (
					'0' => $this->getRequest()->getParam('street1'),
					'1' => $this->getRequest()->getParam('street2'),
				),
			'city' => $this->getRequest()->getParam('city'),
			'region_id' => $this->getRequest()->getParam('region_id'),
			'region' => $this->getRequest()->getParam('region'),
			'postcode' => $this->getRequest()->getParam('postcode'),
			'country_id' => $this->getRequest()->getParam('country_id'),
			'telephone' => $this->getRequest()->getParam('telephone'),
			'save_in_address_book' => '1',
			'same_as_billing' => '1',
		);

		$data = $this->getRequest()->getPost('shipping', $_custom_address);
		$customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);

		$getOnePageObj2 = Mage::getSingleton('checkout/type_onepage');
		
		try {
			$result = $getOnePageObj2->saveShipping($data, $customerAddressId);
		}
		catch (Exception $ex) {
			Zend_Debug::dump($ex->getMessage());
		}
		
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId());

		$customAddress = Mage::getModel('customer/address')->load($customer->getDefaultShipping());
		
		//Mage::getSingleton('checkout/session')->getQuote()->setShippingAddress(Mage::getSingleton('sales/quote_address')->importCustomerAddress($customAddress));
		
		if(empty($result)){
			$result = 'saved';
		}

		$data = json_encode((array)$result);
		header('Content-Type: application/json');
		echo ($data);
	}

}

class CapturePlus_Interactive_Find_v2_00
{

   //Credit: Thanks to Stuart Sillitoe (http://stu.so/me) for the original PHP that these samples are based on.

   private $Key; //The key to use to authenticate to the service.
   private $SearchTerm; //The search term to find. If the LastId is provided, the SearchTerm searches within the results from the LastId.
   private $LastId; //The Id from a previous Find or FindByPosition.
   private $SearchFor; //Filters the search results.
   private $Country; //The name or ISO 2 or 3 character code for the country to search in. Most country names will be recognised but the use of the ISO country code is recommended for clarity.
   private $LanguagePreference; //The 2 or 4 character language preference identifier e.g. (en, en-gb, en-us etc).
   private $Data; //Holds the results of the query

   function CapturePlus_Interactive_Find_v2_00($Key, $SearchTerm, $LastId, $SearchFor, $Country, $LanguagePreference)
   {
      $this->Key = $Key;
      $this->SearchTerm = $SearchTerm;
      $this->LastId = $LastId;
      $this->SearchFor = $SearchFor;
      $this->Country = $Country;
      $this->LanguagePreference = $LanguagePreference;
   }

   function MakeRequest()
   {
      $url = "http://services.postcodeanywhere.co.uk/CapturePlus/Interactive/Find/v2.00/xmla.ws?";
      $url .= "&Key=" . urlencode($this->Key);
      $url .= "&SearchTerm=" . urlencode($this->SearchTerm);
      $url .= "&LastId=" . urlencode($this->LastId);
      $url .= "&SearchFor=" . urlencode($this->SearchFor);
      $url .= "&Country=" . urlencode($this->Country);
      $url .= "&LanguagePreference=" . urlencode($this->LanguagePreference);

      //Make the request to Postcode Anywhere and parse the XML returned
      $file = simplexml_load_file($url);
      //Check for an error, if there is one then throw an exception
      if ($file->Columns->Column->attributes()->Name == "Error") 
      {
         throw new Exception("[ID] " . $file->Rows->Row->attributes()->Error . " [DESCRIPTION] " . $file->Rows->Row->attributes()->Description . " [CAUSE] " . $file->Rows->Row->attributes()->Cause . " [RESOLUTION] " . $file->Rows->Row->attributes()->Resolution);
      }

      //Copy the data
      if ( !empty($file->Rows) )
      {
         foreach ($file->Rows->Row as $item)
         {
			 $this->Data[] = array('Id'=>$item->attributes()->Id,'Text'=>$item->attributes()->Text,'Highlight'=>$item->attributes()->Highlight,'Cursor'=>$item->attributes()->Cursor,'Description'=>$item->attributes()->Description,'Next'=>$item->attributes()->Next);
         }
      }
   }

   function HasData()
   {
      if ( !empty($this->Data) )
      {
         return $this->Data;
      }
	  else{
		return $this->Data[] = array('Text'=>"Invalid Addess");
	  }
      return false;
   }
}
