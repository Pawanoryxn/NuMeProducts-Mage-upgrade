<?php 
class Atypicalbrands_Opencartusertomagento_IndexController extends Mage_Core_Controller_Front_Action 
{
	public function indexAction() 
    { 
     $this->loadLayout(); 
     $this->renderLayout(); 
    }  
	
	public function loginAction() 
    { 
    	$session = Mage::getSingleton('customer/session'); 
        if ($session->isLoggedIn()) {
        	//$session->setRedirectWithCookieCheck('*/*/'); 
        // is already login redirect to account page 
            return; 
        } 
 
        $result = array('success' => false); 
 
        if ($this->getRequest()->isPost()) 
        { 
            $login_data = $this->getRequest()->getPost('login'); 
            if (empty($login_data['username']) || empty($login_data['password'])) { 
                $result['error'] = Mage::helper('onepagecheckout')->__('Login and password are required.'); 
            } 
            else 
            {
            	$log = FALSE; 
                try 
                { 
                    $log = $session->login($login_data['username'], $login_data['password']);
					Zend_Debug::dump($log);
					//$session->setCustomerAsLoggedIn( $session->getCustomer() );
                    $result['success'] = true; 
                    $result['redirect'] = Mage::getUrl('*/*/index'); 
                } 
                catch (Mage_Core_Exception $e) 
                { 
                    switch ($e->getCode()) { 
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED: 
                            $message = Mage::helper('customer')->__('Email is not confirmed. <a href="%s">Resend confirmation email.</a>', Mage::helper('customer')->getEmailConfirmationUrl($login_data['username'])); 
                            break; 
                        default: 
                            $message = $e->getMessage(); 
                    }
                    $session->addError($message);
                    $session->setUsername($login_data['username']); 
                }
				if ($log != TRUE){
					
					$OCResponse = $this->_checkOCForCustomer($login_data['username'], $login_data['password']);
					if ($OCResponse == FALSE){
						$this->_redirect('customer/account/create'); 
					} else {
						Mage::register('customerInfo', $OCResponse);
						$this->_redirect('opencartusertomagento/index/migrate');
						return;
					}
					
					
				}
				
            } 
        } 
 
        //$this->_redirect('customer/account/'); 		 
    }

	public function migrateAction(){
		//$email = Mage::app()->getRequest()->getParam('email').	
		
		$this->loadLayout(); 
     	
		$layout = $this->getLayout();
		$block = $layout->getBlock('opencartusertomagento_migrate');
		
		$customerInfo = Mage::registry("customerInfo");
		
		$block->setData('firstname', $customerInfo['firstname']);
		$block->setData('lastname', $customerInfo['lastname']);
		$block->setData('address1', $customerInfo['address1']);
		$block->setData('address2', $customerInfo['address2']);
		$block->setData('city', $customerInfo['city']);
		$block->setData('region', $customerInfo['region']);
		$block->setData('zip', $customerInfo['zip']);
		$block->setData('telephone', $customerInfo['telephone']);
		$block->setData('fax', $customerInfo['fax']);
		
		
		Mage::unregister('customerInfo');
		
		$this->renderLayout();
	}


	public function doMigrateAction(){
		
		$session = Mage::getSingleton('customer/session'); 
        if ($session->isLoggedIn()) { 
        // is already login redirect to account page 
            return; 
        } 
		
		if ($this->getRequest()->isPost()) 
        {
        	$migrate_data = $this->getRequest()->getPost('migrate'); 
			
			 if (TRUE) {    //TODO: check to see if proper data was filled out.  
                $result['error'] = Mage::helper('onepagecheckout')->__('Please fill out all required information.'); 
            } 
            else 
            {
            	
			} 
		}
		
		$websiteId = Mage::app()->getWebsite()->getId();
		$store = Mage::app()->getStore();
		
		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId = $websiteId;
		$customer->setStore($store);
		
		$customer->setFirstname("Douglas");
		$customer->setLastname("Radburn");
		$customer->setEmail("hello@douglasradburn.co.uk");
		$customer->setPasswordHash(md5("myReallySecurePassword"));
		$customer->save();
		
		$address = Mage::getModel("customer/address");
		// you need a customer object here, or simply the ID as a string.
		$address->setCustomerId($customer->getId());
		$address->setFirstname($customer->getFirstname());
		$address->setLastname($customer->getLastname());
		$address->setCountryId("GB"); //Country code here
		$address->setStreet("A Street");
		$address->setPostcode("LS253DP");
		$address->setCity("Leeds");
		$address->setTelephone("07789 123 456");
		 
		$address->save();
		
	}

	public function migrateallAction(){
		$memory = ini_get('memory_limit');
		$timeLimit = ini_get('max_execution_time');
		ini_set('memory_limit', '10280M');
		ini_set('max_execution_time', 36000);
		
		$db = mysqli_connect('database-server', 'charles','!Charles123', 'OC_Customers');
		$query = "SELECT * FROM customer WHERE customer_id > 97435;";
		$customers = $db->query($query);
		
		$newsletterSubs = array();
		
		while ($customer = $customers->fetch_assoc()){
			
			$customerID = $customer['customer_id'];
			$customer_info['email'] = $customer['email'];
			$customer_info['password'] = $customer['password'];
			$customer_info['firstname'] = $customer['firstname'];
			$customer_info['lastname'] = $customer['lastname'];
			$customer_info['telephone'] = $customer['telephone'];
			$customer_info['fax'] = $customer['fax'];
			
			if ($customer['newsletter'] == 1){
				$newsletterSubs[] = $customer['email'];
			}
			
			$query = "SELECT * FROM address WHERE customer_id = ".$customerID;
			$result = $db->query($query);
			if ($result == FALSE){
				continue;
			}
			$address = $result->fetch_assoc();
			$customer_info['address1'] = $address['address_1'];
			$customer_info['address2'] = $address['address_2'];
			$customer_info['city'] = $address['city'];
			$customer_info['zip']= $address['postcode'];
			
			$countryID = $address['country_id'];
			$zoneID = $address['zone_id'];
			
			$query = "SELECT * FROM country WHERE country_id = ".$countryID;
			$result = $db->query($query);
			if ($result == FALSE){
				continue;
			}
			$country_info = $result->fetch_assoc();
			$customer_info['country'] = $country_info['name'];
			$customer_info['country_iso2'] = $country_info['iso_code_2'];
			$customer_info['country_iso3'] = $country_info['iso_code_3'];
			
			$query = "SELECT * FROM zone WHERE zone_id = ".$zoneID;
			$result = $db->query($query);
			if ($result == FALSE){
				continue;
			}
			$region_info = $result->fetch_assoc();
			$customer_info['region'] = $region_info['name'];
			$customer_info['region_code'] = $region_info['code'];
			
			
			$newCustomer = Mage::getModel("customer/customer");
			
			try{
				$newCustomer->setWebsiteId(Mage::app()->getWebsite()->getId());
				$newCustomer->setStore(Mage::app()->getStore());
				
				$newCustomer->setFirstname($customer_info['firstname']);
				$newCustomer->setLastname($customer_info['lastname']);
				$newCustomer->setEmail($customer_info['email']);
				$newCustomer->setPasswordHash($customer_info['password'].':');
				$newCustomer->save();
			} catch(Exception $e){
				Mage::log('Customer Save Error::' . $e->getMessage());
			}
			
			$newAddress = Mage::getModel("customer/address");
			
			$regionModel = Mage::getModel('directory/region')->loadByName($customer_info['region'], $customer_info['country_iso2']);
			$regionId = $regionModel->getId();
			
			//Remove non-numeric characters from telephone numbers
			$customer_info['telephone'] = preg_replace('/\D/', '', $customer_info['telephone']);

			$dataAddress = array(
				'firstname' => $customer_info['firstname'],
				'lastname' => $customer_info['lastname'],
				'street' => array($customer_info['address1'], $customer_info['address2']),
				'city' =>$customer_info['city'],
				'region' =>$customer_info['region'],
				'region_id' => $regionId,
				'postcode' => $customer_info['zip'],
				'country_id' => $customer_info['country_iso2'],
				'telephone' => $customer_info['telephone'],
			);
			
			try {
			    $newAddress
			        ->addData($dataAddress)
					->setCustomerId($newCustomer->getId())
					->setIsDefaultBilling('1')
		            ->setIsDefaultShipping('1')
		            ->setSaveInAddressBook('1')
			        ->save();
			} catch(Exception $e){
			    Mage::log('Address Save Error::' . $e->getMessage());
			}
			

			try{
				$newCustomer->addAddress($newAddress);
				$newCustomer->save();
			} catch(Exception $e){
				Mage::log('Customer Save Error::' . $e->getMessage());
			}
			
			unset($customer_info);
			unset($dataAddress);
			unset($customer);
			
		}
		
		foreach ($newsletterSubs as $subEmail) {
		    # create new subscriber without sending confirmation email
		    Mage::getModel('newsletter/subscriber')->setImportMode(true)->subscribe($subEmail);
		    
		    # get just generated subscriber
		    $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($subEmail);
		
		    # change status to "subscribed" and save
		    $subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
		    $subscriber->save();
		}

		unset($newsletterSubs);
		unset($customers);
		ini_set('memory_limit', $memory);
		ini_set('max_execution_time', $timeLimit);
		$db->close();
	}

	public function migrateoneAction(){
		
		$db = mysqli_connect('localhost', 'charles','!Charles123', 'OC_Customers');
		$query = "SELECT * FROM customer WHERE email = 'rockoc3@gmail.com';";
		$customers = $db->query($query);
		
		$customer = $customers->fetch_assoc();
			
			$customerID = $customer['customer_id'];
			$customer_info['email'] = $customer['email'];
			$customer_info['password'] = $customer['password'];
			$customer_info['firstname'] = $customer['firstname'];
			$customer_info['lastname'] = $customer['lastname'];
			$customer_info['telephone'] = $customer['telephone'];
			$customer_info['fax'] = $customer['fax'];
			
			$query = "SELECT * FROM address WHERE customer_id = ".$customerID;
			$result = $db->query($query);
			if ($result == FALSE){
				continue;
			}
			$address = $result->fetch_assoc();
			$customer_info['address1'] = $address['address_1'];
			$customer_info['address2'] = $address['address_2'];
			$customer_info['city'] = $address['city'];
			$customer_info['zip']= $address['postcode'];
			
			$countryID = $address['country_id'];
			$zoneID = $address['zone_id'];
			
			$query = "SELECT * FROM country WHERE country_id = ".$countryID;
			$result = $db->query($query);
			if ($result == FALSE){
				continue;
			}
			$country_info = $result->fetch_assoc();
			$customer_info['country'] = $country_info['name'];
			$customer_info['country_iso2'] = $country_info['iso_code_2'];
			$customer_info['country_iso3'] = $country_info['iso_code_3'];
			
			$query = "SELECT * FROM zone WHERE zone_id = ".$zoneID;
			$result = $db->query($query);
			if ($result == FALSE){
				continue;
			}
			$region_info = $result->fetch_assoc();
			$customer_info['region'] = $region_info['name'];
			$customer_info['region_code'] = $region_info['code'];
			
			
			$newCustomer = Mage::getModel("customer/customer");
			
			try{
				$newCustomer->setWebsiteId(Mage::app()->getWebsite()->getId());
				$newCustomer->setStore(Mage::app()->getStore());
				
				$newCustomer->setFirstname($customer_info['firstname']);
				$newCustomer->setLastname($customer_info['lastname']);
				$newCustomer->setEmail($customer_info['email']);
				$newCustomer->setPasswordHash($customer_info['password'].':');
				$newCustomer->save();
			} catch(Exception $e){
				Mage::log('Customer Save Error::' . $e->getMessage());
			}
			
			$newAddress = Mage::getModel("customer/address");
			
			$regionModel = Mage::getModel('directory/region')->loadByName($customer_info['region'], $customer_info['country_iso2']);
			$regionId = $regionModel->getId();
			
			// you need a customer object here, or simply the ID as a string.
			$dataAddress = array(
				'firstname' => $customer_info['firstname'],
				'lastname' => $customer_info['lastname'],
				'street' => array($customer_info['address1'], $customer_info['address2']),
				'city' =>$customer_info['city'],
				'region' =>$customer_info['region'],
				'region_id' => $regionId,
				'postcode' => $customer_info['zip'],
				'country_id' => $customer_info['country_iso2'],
				'telephone' => $customer_info['telephone'],
			);
			
			try {
			    $newAddress
			        ->addData($dataAddress)
					->setCustomerId($newCustomer->getId())
					->setIsDefaultBilling('1')
		            ->setIsDefaultShipping('1')
		            ->setSaveInAddressBook('1')
			        ->save();
			} catch(Exception $e){
			    Mage::log('Address Save Error::' . $e->getMessage());
			}
			

			try{
				$newCustomer->addAddress($newAddress);
				$newCustomer->save();
			} catch(Exception $e){
				Mage::log('Customer Save Error::' . $e->getMessage());
			}
			
		

	}

	public function reviewAction(){
		$memory = ini_get('memory_limit');
		$timeLimit = ini_get('max_execution_time');
		ini_set('memory_limit', '2056M');
		ini_set('max_execution_time', 36000);
		
		$db = mysqli_connect('database-server', 'charles','!Charles123', 'nume_opencart_archive') or die();
		$query = "SELECT * FROM review;";
		$reviews = $db->query($query);
		
		$extraData = array();
		$count = 1;
		
		while ($review = $reviews->fetch_assoc()){
		
			$mageProductID = $this->_productNumberTranslate($review['product_id']);
			
			if ($mageProductID == -1){
				$query = "INSERT INTO unmatched_reviews (review_id, product_id, customer_id, author, text, rating, status, date_added, date_modified) VALUES (";
				$query = $query . $review['review_id'].", ".$review['product_id'].", ".$review['customer_id'].", '".$review['author']."', '".$review['text']."', ".$review['rating'].", ".$review['status'].", '".$review['date_added']."', '".$review['date_modified']."');";
				Zend_Debug::dump($query);
				try {
					$unmatchedResult = $db->query($query);
					if (!$unmatchedResult){
							Zend_Debug::dump('couldnt save unmatched');
							Zend_Debug::dump($review);
						} else {
							Zend_Debug::dump("saved unmatched");
						}
				} catch (Exception $e){
					//Zend_Debug::dump($query);
					Zend_Debug::dump('couldnt save unmatched');
					Zend_Debug::dump($review);
				}
				continue;
			} else {
				
				$author = '';
				if (strpos($review['author'],'Posted by ') !== false){
					$stringArray = explode(' ', $review['author']);
					$author = $stringArray[2];
				} else {
					$author = $review['author'];
				}
				
				$title = substr($review['text'], 0, 25);
				while (substr($title, -1, 1) != ' ' || $title == ''){
					Zend_Debug::dump($title);
					$title = substr($title, 0, strlen($title)-1);    //remove any letters that got cut off mid word
					if ($title == false){
						break;
					}
				}
				if ($title == ''){
					$title = substr($review['text'], 0, 20) . '...';
				} else {
					$title = rtrim($title);  //do it once more, since the while loop stopped when it found a space,
				}
				$titleLength = strlen($title);   //get length
				Zend_Debug::dump(strlen($review['text']));
				Zend_Debug::dump(($titleLength-1));
				$body = $review['text'];
				$title = $title . '...';
				$custID = $review['customer_id'];
				$customerInfo = '';
				$mageCustID = null;
				$rID = -1;
				if (isset($review['customer_id']) && $custID > 0){
					$query ="SELECT firstname, lastname, email, telephone FROM customer WHERE customer_id = ".$custID;
					$ocCustomerR = $db->query($query);
					if ($ocCustomerR == FALSE){
						//do nothing
					} else {
						$ocCustomer = $ocCustomerR->fetch_assoc();
						if($ocCustomer != FALSE){
							$customerInfo = $ocCustomer['email'];
							$customer = Mage::getModel("customer/customer");
							$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
							$customer->loadByEmail($customerInfo); //load customer by email id
							if ($customer->getId()) {
						        $mageCustID = $customer->getId();
						    }
						}
					}
					unset($ocCustomerR);
				}
				
				try{
					$newReview = Mage::getModel('review/review');
					$newReview->setEntityPkValue($mageProductID);//product id
					$newReview->setRatingSummary($review['rating']);
					$newReview->setTitle($title);
					$newReview->setDetail($body);
					$newReview->setEntityId($newReview->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE));
					$newReview->setStoreId(Mage::app()->getStore()->getId());
					if ($review['status'] == 1){
						$status = Mage_Review_Model_Review::STATUS_APPROVED;
					} else {
						$status = Mage_Review_Model_Review::STATUS_PENDING;
					}
					$newReview->setStatusId($status);
					$newReview->setCustomerId($mageCustID);//null is for administrator
					$newReview->setNickname($author);
					$rID = $newReview->getId();
					Zend_Debug::dump($rID);
					$newReview->setReviewId($rID);
					$newReview->setStores(array(Mage::app()->getStore()->getId()));
					$newReview->save();
					for ($j = 1; $j <= 3 ; $j++){
						try{
							$_rating = Mage::getModel('rating/rating');
							$_rating->setRatingId($j)
								->setReviewId($newReview->getId())
								->addOptionVote($review['rating'], $mageProductID);
						} catch(Exception $e){
							Mage::log('Product ID: '. $mageProductID .' - Rating Save Error:' . $e->getMessage());
							Zend_Debug::dump($_rating);
							Zend_Debug::dump($newReview);
							die('Product ID: '. $mageProductID .' - Rating Save Error:' . $e->getMessage());
						}
					}
//					$newReview->setEntityId(1);
					$newReview->save();
					$newReview->aggregate();
					
					try {
						$extraData[] = array(
							'mager_id' => $count,
							'ocr_id' => $review['review_id'],
							'mproduct_id' => $mageProductID,
							'created_at' => $review['date_added'],
							'oc_status' => $review['status']
						);
						$query = "INSERT INTO matched (mager_id, ocr_id, created_at, oc_status, mproduct_id) VALUES (".$count.", ".$review['review_id'].", '".$review['date_added']."', ".$review['status'].", ".$mageProductID.")";
						Zend_Debug::dump($query);
						$insertResult = $db->query($query);
						if (!$insertResult){
							Zend_Debug::dump("couldn't save stats and created at date... oc review id: ".$review['review_id'] );
						} else {
							Zend_Debug::dump("saved created date");
						}
					} catch (Exception $e){
						Zend_Debug::dump("couldn't save stats and created at date... oc review id: ".$review['review_id']. 'with error: '.$e->getMessage() );
						die('Product ID: '. $mageProductID .' - extra data Save Error:' . $e->getMessage());
					}
					
					$count++;
					
				} catch(Exception $e){
					Mage::log('Product ID: '. $mageProductID .' - Review Save Error:' . $e->getMessage());
					Zend_Debug::dump($newReview);
					die('Product ID: '. $mageProductID .' - Review Save Error:' . $e->getMessage());
				}
			}
		
		}
		
		$db2 = mysqli_connect('database-server', 'charles','!Charles123', 'nume_mage') or die();
		
		$query = "SELECT * FROM matched";
		$matchedRows = $db->query($query);
		
		if ($matchedRows == false){
			Zend_Debug::dump("COULD NOT GET MATCHED ROWS!");
		} else {

			while($eData = $matchedRows->fetch_assoc()){
				$query = "UPDATE review SET created_at = '". $eData['created_at']. "' WHERE review_id = ".$eData['mager_id']. " AND entity_pk_value = ".$eData['mproduct_id'];
				Zend_Debug::dump($query);
				try {
					$extraDataResult = $db2->query($query);
					//$extraDataResult = true;
					if (!$extraDataResult){
						Zend_Debug::dump('error saving created at date. error: '. $db->error);
						//Zend_Debug::dump('Query: '.$query);
					} else {
						Zend_Debug::dump('created at date saved!');
					}
				} catch (Exception $e){
					Zend_Debug::dump('error saving created at date. error: '.$db->error);
					//Zend_Debug::dump('Query: '.$query);
					Zend_Debug::dump('Eception: '.$e->getMessage());
				}
			}
		}

		$db->close();
		$db2->close();

		ini_set('memory_limit', $memory);
		ini_set('max_execution_time', $timeLimit);
	}

	protected function _productNumberTranslate($OCprodID){
		
		$mageProductID = -1;
		
		switch($OCprodID){
			case 268:
				$mageProductID = 330;
				break;
			case 437:
				$mageProductID = 333;
				break;
			case 274:
				$mageProductID = 342;
				break;
			case 276:
				$mageProductID = 342;
				break;
			case 281:
				$mageProductID = 339;
				break;
			case 284:
				$mageProductID = 385;
				break;
			case 288:
				$mageProductID = 384;
				break;
			case 291:
				$mageProductID = 340;
				break;
			case 294:
				$mageProductID = 337;
				break;
			case 298:
				$mageProductID = 449;
				break;
			case 301:
				$mageProductID = 235;
				break;
			case 302:
				$mageProductID = 241;
				break;
			case 303:
				$mageProductID = 236;
				break;
			case 304:
				$mageProductID = 260;
				break;
			case 305:
				$mageProductID = 242;
				break;
			case 306:
				$mageProductID = 243;
				break;
			case 307:
				$mageProductID = 245;
				break;
			case 308:
				$mageProductID = 244;
				break;
			case 309:
				$mageProductID = 328;
				break;
			case 311:
				$mageProductID = 327;
				break;
			case 316:
				$mageProductID = 325;
				break;
			case 319:
				$mageProductID = 326;
				break;
			case 359:
				$mageProductID = 334;
				break;
			case 378:
				$mageProductID = 238;
				break;
			case 379:
				$mageProductID = 248;
				break;
			case 380:
				$mageProductID = 360;
				break;
			case 381:
				$mageProductID = 239;
				break;
			case 382:
				$mageProductID = 237;
				break;
			case 383:
				$mageProductID = 240;
				break;
			case 384:
				$mageProductID = 247;
				break;
			case 385:
				$mageProductID = 361;
				break;
			case 386:
				$mageProductID = 249;
				break;
			case 387:
				$mageProductID = 239;
				break;
			case 390:
				$mageProductID = 362;
				break;
			case 391:
				$mageProductID = 363;
				break;
			case 392:
				$mageProductID = 331;
				break;
			case 393:
				$mageProductID = 332;
				break;
			case 394:
				$mageProductID = 386;
				break;
			case 395:
				$mageProductID = 335;
				break;
			case 396:
				$mageProductID = 338;
				break;
			case 397:
				$mageProductID = 329;
				break;
			case 398:
				$mageProductID = 219;
				break;
			case 399:
				$mageProductID = 444;
				break;
			case 407:
				$mageProductID = 364;
				break;
			case 408:
				$mageProductID = 365;
				break;
			case 434:
				$mageProductID = 371;
				break;
			case 439:
				$mageProductID = 370;
				break;
			case 442:
				$mageProductID = 252;
				break;
			case 443:
				$mageProductID = 259;
				break;
			case 444:
				$mageProductID = 336;
				break;
			case 445:
				$mageProductID = 255;
				break;
			case 446:
				$mageProductID = 375;
				break;
			case 450:
				$mageProductID = 379;
				break;
			case 455:
				$mageProductID = 380;
				break;
						
			
			
			default:
				$mageProductID = -1;
				break;
		}

		return $mageProductID;
		
	}


	protected function _checkOCForCustomer($email = null, $password = null){
		
		if (!$email && !$password){
			$db = mysqli_connect('localhost', 'charles','!Charles123', 'OC_Customers');
			$query = "SELECT * FROM customer WHERE email = '".$email."' AND password = '".md5($password)."';";
			$result = $db->query($query);
			
			if ($result == FALSE){
				$db->close();
				return false;
			} else {
				$customerID = -1;
				$customer_info = array();
				$customer = $result->fetch_assoc();
				if (isset($customer['customer_id'])){
					$customerID = $customer['customer_id'];
					$customer_info['firstname'] = $customer['firstname'];
					$customer_info['lastname'] = $customer['lastname'];
					$customer_info['telephone'] = $customer['telephone'];
					$customer_info['fax'] = $customer['fax'];
					
					$query = "SELECT * FROM address WHERE customer_id = ".$customerID;
					$result = $db->query($query);
					$address = $result->fetch_assoc();
					$customer_info['address1'] = $address['address_1'];
					$customer_info['address2'] = $address['address_2'];
					$customer_info['city'] = $address['city'];
					$customer_info['zip']= $address['postcode'];
					
					$countryID = $address['country_id'];
					$zoneID = $address['zone_id'];
					
					$query = "SELECT * FROM country WHERE country_id = ".$countryID;
					$result = $db->query($query);
					$country_info = $result->fetch_assoc();
					$customer_info['country'] = $country_info['name'];
					$customer_info['country_iso2'] = $country_info['iso_code_2'];
					$customer_info['country_iso3'] = $country_info['iso_code_3'];
					
					$query = "SELECT * FROM zone WHERE zone_id = ".$zoneID;
					$result = $db->query($query);
					$region_info = $result->fetch_assoc();
					$customer_info['region'] = $region_info['name'];
									
					$db->close();
					
					return $customer_info;
				
				} else{
					$db->close();
					return FALSE;
				}
				
			}
			
			
		} else {
			return false;
		}
		
	}
} 
?> 
