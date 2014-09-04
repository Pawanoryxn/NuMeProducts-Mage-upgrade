<?php

/**
 * 
 */

class Atypicalbrands_Promocampaign_Model_Observer {

    protected $_allRules = null;

    public function restrictRates($observer) {
        $request = $observer->getRequest();
        $result = $observer->getResult();

        // retrive all the shipping rate
        $rates = $result->getAllRates();
        if (!count($rates)) {
            return $result;
        }
        
        //  check each rate for Carrier name and Price
        foreach ($rates as $rate) {
            if ($rate->getCarrier() == 'shipping_with_coupon') {
                $result->reset();
                $result->append($rate);
            }
        }
        return $result;
    }

}
