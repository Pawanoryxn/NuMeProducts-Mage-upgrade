<?php
//require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Promo/QuoteController.php');
include_once('Mage/Adminhtml/controllers/Promo/QuoteController.php');
class Atypicalbrands_Promocampaign_Adminhtml_Promo_QuoteController extends Mage_Adminhtml_Promo_QuoteController
{
  
  
  
   /**
     * Promo quote save action
     *
     */
    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                /** @var $model Mage_SalesRule_Model_Rule */
                $model = Mage::getModel('salesrule/rule');
                Mage::dispatchEvent(
                    'adminhtml_controller_salesrule_prepare_save',
                    array('request' => $this->getRequest()));
                $data = $this->getRequest()->getPost();
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                $id = $this->getRequest()->getParam('rule_id');
                $ownerofcampaign = $this->getRequest()->getParam('ownerofcampaign');
                if ($id) {
                    $model->load($id);
                    $newRule = false;
                    if ($id != $model->getId()) {
                        $newRule = true;
                        Mage::throwException(Mage::helper('salesrule')->__('Wrong rule specified.'));
                    }
                }

                $session = Mage::getSingleton('adminhtml/session');

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }

        
                if (isset($data['simple_action']) && $data['simple_action'] == 'by_percent'
                && isset($data['discount_amount'])) {
                    $data['discount_amount'] = min(100,$data['discount_amount']);
                }
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
        if (isset($data['set_flatrate_by_region']) && $data['set_flatrate_by_region'] == 1){
          $couponShippingRate = array();
          
          if (isset($data['set_flatrate_shipping_us'])){
            
            $couponShippingRate['US'] = (int)$data['set_flatrate_shipping_us'];
          }
          
          if (isset($data['set_flatrate_shipping_ca'])){
            $couponShippingRate['CA'] = (int)$data['set_flatrate_shipping_ca'];
          }
                              $mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
                              if(in_array($_FILES['file']['type'],$mimes)):
                              if ($_FILES["file"]["error"] > 0) {
                              echo "Error: " . $_FILES["file"]["error"] . "<br>";
                              } else {
                                if (file_exists("media/shippingfile/" . $_FILES["file"]["name"])) {
                                  $newfilename = rand(0,1000000).".csv";
                                  move_uploaded_file($_FILES["file"]["tmp_name"],"media/shippingfile/" . $newfilename);
                                    echo "Stored in: " . "media/shippingfile/" . $_FILES["file"]["name"];
                                    $fileLocation = "media/shippingfile/". $_FILES['file']['name'];
                                    $file = fopen($fileLocation,"r");
                                    while(! feof($file))
                                    {
                                      $csvContent  = fgetcsv($file);
                                    echo "<pre>";
                                    print_r($csvContent);
                                    echo "</pre>";
                                    $contentCountriesSize = strlen($csvContent[0]);
                                    if($contentCountriesSize == 2  AND is_numeric($csvContent[1]))
                                      if($csvContent[0] != "US" OR $csvContent[0] != "CA")
                                    $couponShippingRate[$csvContent[0]] = (int)$csvContent[1];
                                    }
                                    fclose($file);
                                  } else {
                                    move_uploaded_file($_FILES["file"]["tmp_name"],"media/shippingfile/" . $_FILES["file"]["name"]);
                                    echo "Stored in: " . "media/shippingfile/" . $_FILES["file"]["name"];
                                    $fileLocation = "media/shippingfile/". $_FILES['file']['name'];
                                    $file = fopen($fileLocation,"r");
                                    while(! feof($file))
                                    {
                                      $csvContent  = fgetcsv($file);
                                    echo "<pre>";
                                    print_r($csvContent);
                                    echo "</pre>";
                                    $contentCountriesSize = strlen($csvContent[0]);
                                    if($contentCountriesSize == 2  AND is_numeric($csvContent[1]))
                                      if($csvContent[0] != "US" OR $csvContent[0] != "CA")
                                    $couponShippingRate[$csvContent[0]] = (int)$csvContent[1];
                                    }
                                    fclose($file);
                                  }
                              }  
                              endif;

                              if (isset($data['set_flatrate_shipping_file']))
                              {
                              echo $data['set_flatrate_shipping_file'];
                              exit;
                              }          

          // setting redeem values in data
          $data['redeem_coupon_status'] = $this->getRequest()->getParam('redeem_coupon_status');
          $data['redeem_coupon_name'] = $this->getRequest()->getParam('redeem_coupon_name');
          // setting redeem values in data
					/*
					Mage::log($couponShippingRate);
					Mage::log(print_r($couponShippingRate,1));
					Mage::log(serialize($couponShippingRate));
					*/
					$data['coupon_shipping_rate'] = serialize($couponShippingRate);
					
				} else if (isset($data['set_flatrate_by_region']) && $data['set_flatrate_by_region'] == 0){
					$data['coupon_shipping_rate'] = serialize(array());  //'a:0:{}';
					unset($data['set_flatrate_shipping_us']);
					unset($data['set_flatrate_shipping_ca']);
					$data['set_flatrate_shipping_us'] = 0;
					$data['set_flatrate_shipping_ca'] = 0;
				}

                $model->loadPost($data);

                $useAutoGeneration = (int)!empty($data['use_auto_generation']);
                $model->setUseAutoGeneration($useAutoGeneration);

                $session->setPageData($model->getData());

                $model->save();
        // update custom data
        $id = $model->getId();
                                               $couponCode = $this->getRequest()->getParam('coupon_code');
        $userArray = Mage::getSingleton('admin/session')->getData();
        $user = Mage::getSingleton('admin/session');
        $userUsername = $user->getUser()->getUsername();
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $dateandtime = date("Y-m-d H:m:s", strtotime('-7 hours', time()));
        $sqlqry = "UPDATE `salesrule` SET `createdats`='$dateandtime',`ownerofcampaign`='$data[ownerofcampaign]',`userupdatelast`='$userUsername' WHERE `rule_id`='$id'";
        $connection->query($sqlqry);
                                                
                                                include('lib/Mandrill/Mandrill-SEND.php');
                                                define('MANDRILL_API_KEY','xLIZABY1xKaILrn5FbvaNg');
                                                $salesmsgUpdate['to_email'] = 'ssingh@atypicalbrands.com';
                                                $salesmsgUpdate['from_name'] = 'tech atypicalbrands ';
                                                $salesmsgUpdate['from_email'] = 'tech@atypicalbrands.com';
                                                if ($newRule == true) {
                                                  $salesmsgUpdatesubject = "New Rule Alert: $userUsername - $id - $couponCode";
                                                } else{
                                                  $salesmsgUpdatesubject = "Updated Rule Alert: $userUsername - $id - $couponCode";
                                                }
                                                $salesmsgUpdatemsg = "Sales Rule Update By : $userUsername
                                                                                          Sales Rule ID : $id
                                                                                            ";
                                                $request_json = '{
                                                   "type":"messages",
                                                   "call":"send",
                                                   "message":{
                                                      "html":"'.$salesmsgUpdatesubject.'",
                                                      "text":"example text",
                                                      "subject":"'.$salesmsgUpdatesubject.'",
                                                      "from_email":"tech@atypicalbrands.com",
                                                      "from_name":"tech AtypicalBrands",
                                                      "to":[
                                                         {
                                                            "email":"mark@atypicalbrands.com",
                                                            "name":"Marketing"
                                                         },
                                                         {
                                                            "email":"cmahecha@atypicalbrands.com",
                                                            "name":"Marketing"
                                                         }
                                                      ],
                                                      "headers":{
                                                         "...":"..."
                                                      },
                                                      "track_opens":true,
                                                      "track_clicks":true,
                                                      "auto_text":true,
                                                      "url_strip_qs":true,
                                                      "tags":[
                                                         "test",
                                                         "example",
                                                         "sample"
                                                      ],
                                                      "google_analytics_domains":[
                                                         "numeproducts.com"
                                                      ],
                                                      "google_analytics_campaign":[
                                                         "..."
                                                      ],
                                                      "metadata":[
                                                         "..."
                                                      ]
                                                   }
                                                }'; 
                                                // $ret = Mandrill::call((array) json_decode($request_json));

                $session->addSuccess(Mage::helper('salesrule')->__('The rule has been saved.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('*/*/edit', array('id' => $id));
                } else {
                    $this->_redirect('*/*/new');
                }
                return;

            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('catalogrule')->__('An error occurred while saving the rule data. Please review the log and try again.'));
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
}
