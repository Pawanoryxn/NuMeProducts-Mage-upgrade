<?php
/**
 * Google Content Config model
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Demac_Atfeed_Model_Config_Attributes extends Varien_Object
{

    protected $_ignoredAttributeCodes = array(
        'custom_design',
        'custom_design_from',
        'custom_design_to',
        'custom_layout_update',
        'gift_message_available',
        'giftcard_amounts',
        'news_from_date',
        'news_to_date',
        'options_container',
        'price_view',
        'sku_type',
        'use_config_is_redeemable',
        'use_config_allow_message',
        'use_config_lifetime',
        'use_config_email_template',
        'tier_price',
        'minimal_price',
        'recurring_profile',
        'shipment_type'
    );

    /**
     * Default ignored attribute types
     *
     * @var array
     */
    protected $_ignoredAttributeTypes = array('hidden', 'media_image', 'image', 'gallery');


    /**
     * Google Account target country info
     *
     * @param int $storeId
     * @return array
     */
    public function getTargetCountryInfo($storeId = null)
    {
        return $this->getCountryInfo($this->getTargetCountry($storeId), null, $storeId);
    }

    /**
     * Google Account target country
     *
     * @param int $storeId
     * @return string Two-letters country ISO code
     */
    public function getTargetCountry($storeId = null)
    {
        return $this->getConfigData('target_country', $storeId);
    }

    /**
     * Google Account target currency (for target country)
     *
     * @param int $storeId
     * @return string Three-letters currency ISO code
     */
    public function getTargetCurrency($storeId = null)
    {
        $country = $this->getTargetCountry($storeId);
        return $this->getCountryInfo($country, 'currency');
    }

    /**
     * Google Content destinations info
     *
     * @param int $storeId
     * @return array
     */
    public function getDestinationsInfo($storeId = null)
    {
        $destinations = $this->getConfigData('destinations', $storeId);
        $destinationsInfo = array();
        foreach ($destinations as $key => $name) {
            $destinationsInfo[$name] = $this->getConfigData($key, $storeId);
        }

        return $destinationsInfo;
    }

    /**
     * Check whether System Base currency equals Google Content target currency or not
     *
     * @param int $storeId
     * @return boolean
     */
    public function isValidDefaultCurrencyCode($storeId = null)
    {
        return Mage::app()->getStore($storeId)->getDefaultCurrencyCode() == $this->getTargetCurrency($storeId);
    }

    /**
     * Google Content supported countries
     *
     * @param int $storeId
     * @return array
     */
    public function getAllowedCountries($storeId = null)
    {
        return $this->getConfigData('allowed_countries', $storeId);
    }

    /**
     * Country info such as name, locale, language etc.
     *
     * @param string $iso two-letters country ISO code
     * @param string $field If specified, return value for field
     * @param int $storeId
     * @return array|string
     */
    public function getCountryInfo($iso, $field = null, $storeId = null)
    {
        $countries = $this->getAllowedCountries($storeId);
        $country = isset($countries[$iso]) ? $countries[$iso] : null;
        $data = isset($country[$field]) ? $country[$field] : null;
        return is_null($field) ? $country : $data;
    }

    /**
     * Returns all attributes (grouped by destination)
     *
     * @return array
     */
    public function getAttributes()
    {
        return Mage::getStoreConfig('sales/atfeed/attributes');
    }

    /**
     * Get flat array with attribute groups
     * where: key - attribute name, value - group name
     *
     * @return array
     */
    public function getAttributeGroupsFlat()
    {
        $groups = $this->getConfigData('attribute_groups');
        $groupFlat = array();
        foreach ($groups as $group => $subAttributes) {
            foreach ($subAttributes as $subAttribute => $value) {
                $groupFlat[$subAttribute] = $group;
            }
        }
        return $groupFlat;
    }

    /**
     * Get array of base attribute names
     *
     * @return array
     */
    public function getBaseAttributes()
    {
        return array_keys($this->getConfigData('base_attributes'));
    }

    /**
     * Check whether debug mode is enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function getIsDebug($storeId)
    {
        return (bool)$this->getConfigData('debug', $storeId);
    }

    /**
     * Returns all required attributes
     *
     * @return array
     */
    public function getRequiredAttributes()
    {
        $requiredAttributes = array();
        foreach ($this->getAttributes() as $group => $attributes) {
            foreach ($attributes as $attributeName => $attribute) {
                if ($attribute['required']) {
                    $requiredAttributes[$attributeName] = $attribute;
                }
            }
        }

        return $requiredAttributes;
    }
}
