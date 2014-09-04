<?php

class Moogento_ShipEasy_Helper_Grid extends Mage_Core_Helper_Abstract
{
    public static function setCountryGroupFilter($collection, $column)
    {
        $condition = $column->getFilter()->getCondition();
        $select = $collection->getSelect();
        foreach($condition as $operator => $value) {
            if ($operator == 'eq') {
                $operator = '=';
            }
            $select->where(self::getCountryGroupSql() . $operator . ' ? ', $value);
        }
    }

    protected static function _quoteCountryId($id)
    {
        return '\'' . trim($id) . '\'';
    }

    
    public static function getCountryGroupSql()
    {
        $countryGroups = Mage::getStoreConfig('moogento_shipeasy/country_groups');
        $sql = '';

        $processedArray = array();

        foreach($countryGroups as $path => $groupInfo) {
            list($varName, $groupId) = explode('_', $path);
            $processedArray[$groupId][$varName] = $groupInfo;
        }

        $countryGroups = $processedArray;
        $count = 0;
        foreach($countryGroups as $groupInfo) {
            $countries = trim($groupInfo['countries']);
            if (empty($countries)) {
                continue;
            }
            $count++;

            $countries = explode(',', $countries);
            $countries = array_map(array('self', '_quoteCountryId'), $countries);
            $countries = implode(',', $countries);

            $sql .= 'IF (`main_table`.`country` IN ('.$countries.'), "'.$groupInfo['label'].'", ';
        }

        $sql .= '" "';

        for($i=0; $i<$count; $i++) {
            $sql .= ')';
        }

        return $sql;
    }

    public function removeHiddenOrderColumns($columns)
    {
        foreach($columns as $index => $column) {
            if (Mage::getStoreConfigFlag('moogento_shipeasy/grid/'.$index.'_show')) {
                unset($columns[$index]);
            }
        }
        return $columns;
    }
}