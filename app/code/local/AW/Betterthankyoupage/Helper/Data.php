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
class AW_Betterthankyoupage_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     *
     */
    const SHARING_BAR_STYLE_SMALL_ICONS_WITH_TITLES = 1;
    const SHARING_BAR_STYLE_SMALL_ICONS             = 2;
    const SHARING_BAR_STYLE_LARGE_ICONS             = 3;

    /**
     * Returns is TBT rewards installed
     */
    public function isSarp2Installed()
    {
        return $this->isModuleOutputEnabled('AW_Sarp2') && @class_exists('AW_Sarp2_Block_Checkout_Onepage_Success');
    }
}