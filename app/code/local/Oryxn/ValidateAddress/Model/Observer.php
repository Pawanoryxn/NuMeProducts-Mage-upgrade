<?php
class Oryxn_ValidateAddress_Model_Observer
{

			public function __construct(){
			
			}
			
			public function PreValidateAddress(Varien_Event_Observer $observer)
			{
				// Below this is the code used to get the correct address from postcode anywhere.
				$update = $observer->getEvent()->getLayout()->getUpdate();
				$update->addHandle('validateaddress_index_index');
				
				
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
						
						if($country_id != "US"){
							$pa = new CapturePlus_Interactive_Find_v2_00 ("XA37-BD49-UF22-ZN66", $address1, "", "Everything", $country_id, "EN");
							$pa->MakeRequest();
							if ($pa->HasData())
							{
							   // result to be alert on Dialog box
								$data = $pa->HasData();
								$received_address = $data[0]["Text"];
								$entered_address = $firm_name.', '.$address1.', '.$zip;
								$str_1 = trim(strtolower($entered_address));
								$str_2 = trim(strtolower($received_address));

								similar_text($str_1, $str_2, $percentage);

								$formated_percents = number_format($percentage);
								//echo $entered_address.'<br/>';
								//echo $formated_percents.'<br/>';
								if($formated_percents <= 85){
									//Mage::getSingleton('core/session')->setPostcodeVariable($received_address);
									//echo $received_address.'<br/>';
								}

							   
							   // If you want to see all alternative address then uncomment below code
							   
							   //foreach ($data as $item)
							   //{
								//  echo $item["Id"] . "<br/>";
								//  echo $item["Text"] . "<br/>";
								//  echo $item["Highlight"] . "<br/>";
								//  echo $item["Cursor"] . "<br/>";
								//  echo $item["Description"] . "<br/>";
							//	  echo $item["Next"] . "<br/>";
							 //  }
							   
							}
						}
					} catch (Exception $e) {            
						Mage::logException($e);
						$response['error'] = $e->getMessage();
					}
				}

				
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
      return false;
   }

}