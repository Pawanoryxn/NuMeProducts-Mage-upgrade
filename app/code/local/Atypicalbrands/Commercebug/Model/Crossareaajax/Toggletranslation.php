<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/
class Atypicalbrands_Commercebug_Model_Crossareaajax_Toggletranslation extends Atypicalbrands_Commercebug_Model_Crossareaajax
{
    public function handleRequest()
    {
        $session = $this->_getSessionObject();        
        $c = $session->getData(Atypicalbrands_Commercebug_Model_Observer::INLINE_TRANSLATION_ON);
        $c = $c == 'on' ? 'off' : 'on';        
        $session->setData(Atypicalbrands_Commercebug_Model_Observer::INLINE_TRANSLATION_ON, $c);        
        $this->endWithHtml('Inline Translation is ' . ucwords($c) .' -- Refresh Page to Access.');        
    }
}