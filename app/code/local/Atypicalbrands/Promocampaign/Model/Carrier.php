<?php

class Atypicalbrands_Promocampaign_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract {

    /**
     * unique identifier for our shipping module
     * @var string $_code
     */
    protected $_code = 'shipping_with_coupon';
    protected $_rate = 0;

    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        if (!$this->getConfigData('active')) {
            Mage::log('The ' . $this->_code . 'my shipping module is not  active.');
            return false;
        }

        $get_rate = new Atypicalbrands_Promocampaign_Model_Getrate;
        $this->_rate = $get_rate->getRate();
        
        // If there is no such a rate
        if($this->_rate !== NULL)
        {
            $handling = $this->getConfigData('handling');
            $result = Mage::getModel('shipping/rate_result');


            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier($this->_code);
            $method->setCarrierTitle('Coupon Flat Rate');

            $method->setMethod('Coupon Mode');
            $method->setMethodTitle('Coupon Flat Rate');

            $method->setCost(5);

            $method->setPrice($this->_rate + $handling);

            $result->append($method);



            return $result;
        }
        
        return NULL;
        
    }

}
