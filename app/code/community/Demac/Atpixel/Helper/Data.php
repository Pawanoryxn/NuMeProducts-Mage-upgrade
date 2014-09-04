<?php
/**
 * Created by JetBrains PhpStorm.
 * User: allenx
 * Date: 04/06/13
 * Time: 10:55 AM
 * To change this template use File | Settings | File Templates.
 */

class Demac_Atpixel_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getAFLinkByOrderId($order_id) {
        if(Mage::getStoreConfig('atpixel/settings/enabled') && Mage::getStoreConfig('atpixel/settings/id'))
        {
            if ($order_id) {
                $order = Mage::getModel('sales/order')->load($order_id);
                $total = round($order->getSubtotal() + $order->getDiscountAmount(),2);

                $order_incr_id = $order->getIncrementId();

                if(Mage::getStoreConfig('atpixel/settings/devmode')) {
                    $order_incr_id = 'test-'.$order_incr_id;
                }

                $url = 'https://'.Mage::getStoreConfig('atpixel/settings/id').'.affiliatetechnology.com/trackingcode_sale.php?mid=1&sec_id=M_14aL4kF3nX8iQ&sale=' . $total . '&orderId=' . $order_incr_id;

                if($order->getCouponCode()) {
                    $url .= '&promo='.urlencode($order->getCouponCode());
                }

                $url .= '&currency=' . Mage::getStoreConfig('atpixel/settings/currency') ;

                if ($order->getId()) {
                    $iframeTag = '<iframe src="'.$url.'" height="1" width="1" frameborder=no border=0 scrolling=no></iframe>';
                    $fbookScript = <<<EOF
<script type="text/javascript">
var fb_param = {};
fb_param.pixel_id = '6006268101157';
fb_param.value = '{$total}';
(function(){
var fpw = document.createElement('script');
fpw.async = true;
fpw.src = '//connect.facebook.net/en_US/fp.js';
var ref = document.getElementsByTagName('script')[0];
ref.parentNode.insertBefore(fpw, ref);
})();
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/offsite_event.php?id=6006268101157&amp;value={$total}" /></noscript>
EOF;
                    return $iframeTag . PHP_EOL . $fbookScript;
                }
            }
        }
        return '';
    }
}