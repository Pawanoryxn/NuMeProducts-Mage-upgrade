<?php

class Moogento_ShipEasy_Block_Adminhtml_Directory_Country_Flag extends Mage_Adminhtml_Block_Template
{
    protected $_countryModel = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('moogento/directory/country/flag.phtml');
    }

    protected function _getCountryImageUrl()
    {
    	if(!$this->getCountryId()) $country_id = 'null';
    	else $country_id = strtolower($this->getCountryId());
        return Mage::getDesign()->getSkinUrl('images/flags/').$country_id.'.png';
    }

    protected function _getCountryTitle()
    {
    	if($this->getCountryId() && $this->getCountryId() != '')
    	{
			if (is_null($this->_countryModel)) {
				$this->_countryModel = Mage::getModel('directory/country')->loadByCode($this->getCountryId());
			}
			$result = '';
			if ($this->_countryModel->getId()) {
				$result = $this->_countryModel->getName();
			}
		}
		else $result = 'null';
        return $result;
    }
}