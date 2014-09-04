<?php
/**
 * i.e. app/code/local/Cross/Pricing/Model/Observer.php
 */
class Google_Events_Model_Observer
{
        public function googleEvents(Varien_Event_Observer $observer)
        {
                $reason = '404';
                $googleUrl = Mage::helper('core/url')->getCurrentUrl();
                $propertyId = Mage::getStoreConfig('google/analytics/account');
                $url = Mage::getBaseUrl();
                //create new server side google analytics object
                include 'lib/Google/ss-ga.class.php';
                $ssga = new ssga( $propertyId, $url );
                $upperCaseCode = strtoupper($couponCode);
                $ssga->set_event( $reason, $googleUrl, $googleUrl, $items );
                $ssga->send();
                $ssga->reset();
        }
}
