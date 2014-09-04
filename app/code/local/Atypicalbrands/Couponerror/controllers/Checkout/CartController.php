<?php

require_once 'Mage/Checkout/controllers/CartController.php';
class Atypicalbrands_Couponerror_Checkout_CartController extends Mage_Checkout_CartController
{

	/**
     * Initialize coupon
     */
    public function couponPostAction()
    {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->_goBack();
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_goBack();
            return;
        }

        try {
			//we want to remove all whitespace from a coupon
			$couponCode = preg_replace('/\s+/', '', $couponCode);
            $codeLength = strlen($couponCode);
            $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;

            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
                ->collectTotals()
                ->save();

                    $items = '';
                    $quote = Mage::getModel('checkout/cart')->getQuote();
                                                            $i=1;
                                                            $cartCount = $this->_getCart()->getQuote()->getItemsCount();
                    foreach ($quote->getAllVisibleItems() as $item){
                                                                       //$items .= '';
                                //$items .= $item->getProduct()->getName();
                        //$items .= '{';
                        $items .= $item->getProduct()->getSku();
                        $items .= '[';
                        $items .= $item->getQty();
                                                                       $items .= ']';
                                                                        if($i < $cartCount){
                                                                            $items .= '+';
                                                                        }else{
                                                                            //$items .= '}';
                                                                        }
                                                                       $i++;
                    }

            if ($codeLength) {
               
                if ($isCodeLengthValid && $couponCode == $this->_getQuote()->getCouponCode()) {
                     if(Mage::getSingleton('core/session')->getCouponCodeMultipleError())
                {
                $this->_getSession()->addError(
                $this->__('The coupon you are attempting to use is not applicable to the items in your cart and/or does not meet the Coupon Rules of Use.  For more information <a href="http://numestyle.freshdesk.com/support/solutions/articles/181216-rules-for-coupon-use" target="_blank">click here</a>.')
                        //$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
                    );
                Mage::getSingleton('core/session')->unsetCouponCodeMultipleError();
                }else{
                    $this->_getSession()->addSuccess(
                        $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode))
                    );
                    }
$reason = 'COUPON-SUCCESS';
		    $shortMessage = 'SUCCESSFUL COUPON ENTRY!!  ~~"'.$couponCode.'"~~   :) '."\n";
                } else {
                    $this->_getSession()->addError(
						$this->__('The coupon you are attempting to use is not applicable to the items in your cart and/or does not meet the Coupon Rules of Use.  For more information <a href="http://numestyle.freshdesk.com/support/solutions/articles/181216-rules-for-coupon-use" target="_blank">click here</a>.')
                        //$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
                    );
					$items = '';
                    $quote = Mage::getModel('checkout/cart')->getQuote();
                                                            $i=1;
                                                            $cartCount = $this->_getCart()->getQuote()->getItemsCount();
					foreach ($quote->getAllVisibleItems() as $item){
                                                                       //$items .= '';
					            //$items .= $item->getProduct()->getName();
						//$items .= '{';
						$items .= $item->getProduct()->getSku();
						$items .= '[';
						$items .= $item->getQty();
                                                                       $items .= ']';
                                                                        if($i < $cartCount){
                                                                            $items .= '+';
                                                                        }else{
                                                                            //$items .= '}';
                                                                        }
                                                                       $i++;
					}
$coupon = Mage::getModel('salesrule/coupon');
$coupon->load($couponCode,'code');
$toDate = $coupon->getToDate();
$currentTime = date('Y-m-d');
if(!$coupon->getId()){
    $reason = 'COUPON-ERROR';
                    $items = '';
                    $quote = Mage::getModel('checkout/cart')->getQuote();
                                                            $i=1;
                                                            $cartCount = $this->_getCart()->getQuote()->getItemsCount();
                    foreach ($quote->getAllVisibleItems() as $item){
                                                                       //$items .= '';
                                //$items .= $item->getProduct()->getName();
                        //$items .= '{';
                        $items .= $item->getProduct()->getSku();
                        $items .= '[';
                        $items .= $item->getQty();
                                                                       $items .= ']';
                                                                        if($i < $cartCount){
                                                                            $items .= '+';
                                                                        }else{
                                                                            //$items .= '}';
                                                                        }
                                                                       $i++;
                    }
}elseif($toDate < $currentTime){
$reason = 'COUPON-ERROR';
}else{
$reason = 'COUPON-ERROR';
}
                                                           //$cartCount = $this->_getCart()->getQuote()->getItemsCount();
					$shortMessage = 'INVALID COUPON ENTRY!!  ~~"'.$couponCode.'"~~   :( '."\n";
					$longMessage = 'INVALID COUPON ENTRY!!  ~~"'.$couponCode.'"~~   :('."\n".'          A user entered an invalid coupon code "'.$couponCode.'" with the following '. $this->_getCart()->getQuote()->getItemsCount() .'items in their cart: '."\n               ".$items;
					Mage::log($shortMessage, null, "couponerror-short.log");
					Mage::log($longMessage, null, "couponerror-long.log");
					// unset($items);
					// unset($longMessage);
					// unset($items);
                }
            } else {
                $reason = 'COUPON-ERROR';
                $couponCode = $oldCouponCode;
                $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
            }

        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }

// if($couponCode = ''){
//     $couponCode = $canceledCouponCode;
// }

$propertyId = Mage::getStoreConfig('google/analytics/account');
$url = Mage::getBaseUrl();
//create new server side google analytics object
include 'lib/Google/ss-ga.class.php';
$ssga = new ssga( $propertyId, $url );
$upperCaseCode = strtoupper($couponCode);
$ssga->set_event( $reason, $upperCaseCode, $items, $items );
$ssga->send();
$ssga->reset();
unset($items);
unset($longMessage);
unset($items);

        $this->_goBack();
    }


	public function cpnerrorAction(){

		$createNode = "+node|Entered Coupon Outputs|Coupon Short Output,Coupon Long Output\r\n";
		$msg = "+log|Coupon Short Output|Entered Coupon Outputs|info|this is log message\r\n";

		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_connect($sock, "10.0.0.4", 28777) or die("Socket Connect Failed");
		socket_write($sock, $createNode, strlen($createNode)) or die("Create Node Failed!");
		socket_write($sock, $msg, strlen($msg)) or die("Send Message Failed!");
		socket_shutdown($sock, 2) or die("Socket Shutdown Failed!");
		socket_close($sock) or die("Socket Close Failed!");

	}


}
