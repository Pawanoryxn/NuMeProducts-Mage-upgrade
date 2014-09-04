<?php

/**
 * Product:       Xtento_OrderStatusImport (1.3.4)
 * ID:            mJUDsdnuj0QF2iAHyWBW1BRT7TLEsSdABAEYeucwpwE=
 * Packaged:      2013-12-11T00:50:14+00:00
 * Last Modified: 2011-11-13T18:35:41+01:00
 * File:          app/code/local/Xtento/OrderStatusImport/Block/System/Config/Frontend/Import/Manual.php
 * Copyright:     Copyright (c) 2013 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderStatusImport_Block_System_Config_Frontend_Import_Manual extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->_getAddRowButtonHtml($this->__('Upload file and run import now'));
    }

    protected function _getAddRowButtonHtml($title) {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setType('button')
                        ->setLabel($this->__($title))
                        ->setOnClick("configForm.submit()")
                        ->toHtml();
    }

}
