<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/
class Atypicalbrands_Commercebug_Model_Graphviz
{
    public function capture()
    {    
        $collector  = new Atypicalbrands_Commercebug_Model_Collectorgraphviz; 
        $o = new stdClass();
        $o->dot = Atypicalbrands_Commercebug_Model_Observer_Dot::renderGraph();
        $collector->collectInformation($o);
    }
    
    public function getShim()
    {
        $shim = Atypicalbrands_Commercebug_Model_Shim::getInstance();        
        return $shim;
    }    
}