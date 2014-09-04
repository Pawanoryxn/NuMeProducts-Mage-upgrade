<?php

/**
 * Product:       Xtento_CustomTrackers (1.4.4)
 * ID:            mJUDsdnuj0QF2iAHyWBW1BRT7TLEsSdABAEYeucwpwE=
 * Packaged:      2013-12-11T00:50:18+00:00
 * Last Modified: 2012-02-10T00:37:15+01:00
 * File:          app/code/local/Xtento/CustomTrackers/Model/Shipping/Carrier/Tracker4.php
 * Copyright:     Copyright (c) 2013 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_CustomTrackers_Model_Shipping_Carrier_Tracker4 extends Xtento_CustomTrackers_Model_Shipping_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'tracker4';

    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('name'));
    }

    public function isTrackingAvailable()
    {
        return parent::isTrackingAvailable();
    }
}