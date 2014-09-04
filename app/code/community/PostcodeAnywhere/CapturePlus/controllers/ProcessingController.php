<?php
class PostcodeAnywhere_CapturePlus_ProcessingController extends Mage_Core_Controller_Front_Action{


   public function indexAction () {
     echo 'test index';
   }
   public function uspsAction (){
					require_once(Mage::getBaseDir('lib') . '/Usps/USPS.php');

					$config['user_id'] = '555ABVGR3887';
					$config['test'] = TRUE;
					$config['secure'] 		= TRUE;
					$config['host'] 		= 'production.shippingapis.com';
					$config['secure_host'] 	= 'secure.shippingapis.com';

					$usps = new USPS($config);

					$firm_name = $this->getRequest()->getParam('firm_name');
					$address1		= $this->getRequest()->getParam('address1');
					$address2		= $this->getRequest()->getParam('address2');
					$city						= $this->getRequest()->getParam('city');
					$state					= $this->getRequest()->getParam('state');
					$zip5						= $this->getRequest()->getParam('zip');
					$addresses = array(
							'0' => array(
								'firm_name' => $firm_name,
								'address1' => $address1,
								'address2' => $address2,
								'city' => $city,
								'state' => $state,
								'zip5' => $zip5,
								'zip4' => ''
							),							
						);

						//RUN ADDRESS STANDARDIZATION REQUEST
					$verified_address = $usps->address_standardization($addresses);
					$data = json_encode((array)$verified_address);
					header('Content-Type: application/json');
					echo ($data);
   }

}
?>