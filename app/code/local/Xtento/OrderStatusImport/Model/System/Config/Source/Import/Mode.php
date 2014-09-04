<?php

/**
 * Product:       Xtento_OrderStatusImport (1.3.4)
 * ID:            mJUDsdnuj0QF2iAHyWBW1BRT7TLEsSdABAEYeucwpwE=
 * Packaged:      2013-12-11T00:50:14+00:00
 * Last Modified: 2010-06-01T15:20:13+02:00
 * File:          app/code/local/Xtento/OrderStatusImport/Model/System/Config/Source/Import/Mode.php
 * Copyright:     Copyright (c) 2013 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderStatusImport_Model_System_Config_Source_Import_Mode
{

    public function toOptionArray()
    {
        $modes[] = array('value' => 'XML', 'label' => Mage::helper('orderstatusimport')->__('XML'));
        $modes[] = array('value' => 'CSV', 'label' => Mage::helper('orderstatusimport')->__('CSV'));
        return $modes;
    }

}
