<?php

/**
 * Product:       Xtento_OrderExport (1.3.4)
 * ID:            mJUDsdnuj0QF2iAHyWBW1BRT7TLEsSdABAEYeucwpwE=
 * Packaged:      2013-12-11T00:50:10+00:00
 * Last Modified: 2012-11-29T18:02:55+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/System/Config/Source/Export/Entity.php
 * Copyright:     Copyright (c) 2013 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_System_Config_Source_Export_Entity
{
    public function toOptionArray()
    {
        return Mage::getSingleton('xtento_orderexport/export')->getEntities();
    }
}