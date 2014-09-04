<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/

class Atypicalbrands_Commercebug_Helper_Cacheclearer
{
    public function clearCache()
    {			
        $shim = $this->getShim()->cleanCache();     
    }
    public function getShim()
    {
        $shim = Atypicalbrands_Commercebug_Model_Shim::getInstance();
        return $shim;
    }    
}