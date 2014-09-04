<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/

class Atypicalbrands_Commercebug_Helper_Collector extends Atypicalbrands_Commercebug_Helper_Abstract
{
    static protected $items;
    static public function saveItem($key, $value)
    {
        self::$items[$key] = $value;
    }
}