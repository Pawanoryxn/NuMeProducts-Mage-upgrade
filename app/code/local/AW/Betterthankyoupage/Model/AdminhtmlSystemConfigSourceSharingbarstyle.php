<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Betterthankyoupage
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


/**
 * 
 */
class AW_Betterthankyoupage_Model_AdminhtmlSystemConfigSourceSharingbarstyle {
    
    /**
     * 
     */
    private $__options = null;
    
    
    /**
     * 
     */
    public function toOptionArray() {
        if (!$this->__options) {
            $this->__options = array(
                array(
                    'label' => Mage::helper('betterthankyoupage')->__('Small icons with titles'),
                    'value' => AW_Betterthankyoupage_Helper_Data::SHARING_BAR_STYLE_SMALL_ICONS_WITH_TITLES
                ),
                array(
                    'label' => Mage::helper('betterthankyoupage')->__('Small icons'),
                    'value' => AW_Betterthankyoupage_Helper_Data::SHARING_BAR_STYLE_SMALL_ICONS
                ),
                array(
                    'label' => Mage::helper('betterthankyoupage')->__('Large icons'),
                    'value' => AW_Betterthankyoupage_Helper_Data::SHARING_BAR_STYLE_LARGE_ICONS
                )
            );
        }
        
        return $this->__options;
    }
}