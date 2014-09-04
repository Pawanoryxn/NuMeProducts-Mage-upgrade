<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Allan MacGregor - Magento Practice Lead <allan@demacmedia.com>
 * Company: Demac Media Inc.
 * Date: 8/9/13
 * Time: 10:11 AM
 */

class Demac_Atpixel_Model_Adminhtml_System_Config_Source_Locale_Currency
{
    public function toOptionArray()
    {
        $locale = Mage::app()->getLocale();

        $currencies = $locale->getTranslationList('currencytoname');
        $options = array();
        $allowed = $locale->getAllowCurrencies();

        foreach ($currencies as $name=>$code) {
            if (!in_array($code, $allowed)) {
                continue;
            }

            $options[] = array(
                'label' => "{$code} - ({$name})",
                'value' => $code,
            );
        }
        return $this->_sortOptionArray($options);
    }

    protected function _sortOptionArray($option)
    {
        $data = array();
        foreach ($option as $item) {
            $data[$item['value']] = $item['label'];
        }
        asort($data);
        $option = array();
        foreach ($data as $key => $label) {
            $option[] = array(
                'value' => $key,
                'label' => $label
            );
        }
        return $option;
    }


}