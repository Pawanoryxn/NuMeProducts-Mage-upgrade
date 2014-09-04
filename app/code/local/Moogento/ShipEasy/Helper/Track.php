<?php

class Moogento_ShipEasy_Helper_Track extends Mage_Core_Helper_Abstract
{
    protected $_carriersConfig = null;
    protected $_trackNoColumnName = null;

    protected function _getDefaultLink()
    {
        return Mage::getStoreConfig('moogento_shipeasy/grid/tracking_number_base_link');
    }

    protected function _getCarriersConfig()
    {
        if (is_null($this->_carriersConfig)) {
            $generatedConfig = array();
            $textConfig = Mage::getStoreConfig('moogento_shipeasy/carriers/base_format');
            $configLines = explode("\n", $textConfig);
            foreach ($configLines as $line) {
                $line = trim($line);
                if (!$line) {
                    continue;
                }
                
                list($prefix, $carrierTitle, $linkFormat) = explode(':', $line, 3);
                $generatedConfig[strtolower($prefix)]['title'] = $carrierTitle;
                $generatedConfig[strtolower($prefix)]['link'] = $linkFormat;
            }
            $this->_carriersConfig = $generatedConfig;
        }
        return $this->_carriersConfig;
    }

    public function getTrackUrl($track)
    {
        $carrierCode = $track->getCarrierCode();
        $carriersConfig = $this->_getCarriersConfig();

        $url = '';

        if ($carrierCode == 'custom') {
            if ($track->getTitle() == 'Custom') {
                $url = str_replace(
                    '#',
                    $track->getNumber(),
                    $this->_getDefaultLink()
                );
            } else {
                foreach($carriersConfig as $prefix => $carrierData) {
                    if ($carrierData['title'] == $track->getTitle()) {
                        $url = str_replace(
                            '#',
                            $track->getNumber(),
                            $carrierData['link']
                        );
                        break;
                    }
                }
            }
        } else {
            $url = str_replace(
                '#',
                $track->getNumber(),
                $this->_getDefaultLink()
            );
            foreach($carriersConfig as $prefix => $carrierData) {
                if (strtolower($carrierData['title']) == $carrierCode) {
                    $url = str_replace(
                        '#',
                        $track->getNumber(),
                        $carrierData['link']
                    );
                    break;
                }
            }
        }
        return $url;
    }

    public function getTrackModel($trackingLine)
    {
        $originalTrackingLine = $trackingLine;
        $trackingLine = strtolower($trackingLine);
        $carriersConfig = $this->_getCarriersConfig();

        $currentCarrier = false;
        $prefix = '';

        foreach($carriersConfig as $carrierCode => $carrierConfigData) {
            if (strpos($trackingLine, strtolower($carrierCode)) === 0) {
                $currentCarrier = $carrierConfigData['title'];
                $prefix = strtolower($carrierCode);
                break;
            }
        }

        if ($currentCarrier) {

            $shippingMethods = Mage::getModel('shipping/config')->getAllCarriers();

            $carrierTitle = $currentCarrier;
            $carrierCode = 'custom';

            foreach($shippingMethods as $method) {
                $title = strtolower($method->getConfigData('title'));
                $code = $method->getCarrierCode();
                if ((strtolower($currentCarrier)==$code) || (strtolower($currentCarrier)==$title)) {
                    $carrierTitle = $method->getConfigData('title');
                    $carrierCode = $code;
                    break;
                }
            }

            $carrierNumber = $originalTrackingLine;//substr($trackingLine, strlen($prefix));
            $track = Mage::getModel('sales/order_shipment_track')
                ->setNumber($carrierNumber)
                ->setCarrierCode($carrierCode)
                ->setTitle($carrierTitle);
        } else {
            $track = Mage::getModel('sales/order_shipment_track')
                ->setNumber($originalTrackingLine)
                ->setCarrierCode('custom')
                ->setTitle('Custom');
        }
        return $track;
    }

    public function getTrackNoColumnName()
    {
        if (is_null($this->_trackNoColumnName)) {
            $_resources  = Mage::getSingleton('core/resource');
            $_connection = $_resources->getConnection('read');
            $describe    = $_connection->describeTable($_resources->getTableName('sales/shipment_track'));

            $this->_trackNoColumnName = 'number';

            foreach($describe as $columnName => $columnData) {
                if (strpos($columnName, 'number') !== false) {
                    $this->_trackNoColumnName = $columnName;
                    break;
                }
            }
        }

        return $this->_trackNoColumnName;
    }
}