<?php

class PostcodeAnywhere_CapturePlus_Block_ScriptInclude extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
		/**
		 * Minified files used by default.
		 * Full files are available (without .min) for debugging.
		 * Only use the minified version if you use JS merging!
		 */
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
    		$head->addCss('captureplus/address-3.20.min.css');
            $head->addJs('captureplus/address-3.20.min.js');
        }
    }
}
