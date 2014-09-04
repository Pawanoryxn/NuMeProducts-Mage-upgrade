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
class AW_Betterthankyoupage_Helper_Sarp2 extends Mage_Core_Helper_Abstract
{
    /**
     * @param Mage_Checkout_Block_Onepage_Success $block
     * @return Mage_Checkout_Block_Onepage_Success
     */
    public function addSectionListToSuccessPage(Mage_Checkout_Block_Onepage_Success &$block)
    {
        $orderInformationSection = Mage::app()->getLayout()->createBlock(
            'betterthankyoupage/sectionOrderinformation', 'section_order_information'
        );
        $orderInformationSection->setTemplate('betterthankyoupage/section_order_information.phtml');
        $block->setChild('section_order_information', $orderInformationSection);
        $block->addSectionBlock('section_order_information');

        $socialSharingBarSection = Mage::app()->getLayout()->createBlock(
            'betterthankyoupage/sectionSocialsharingbar', 'section_social_sharing_bar'
        );
        $socialSharingBarSection->setTemplate('betterthankyoupage/section_social_sharing_bar.phtml');
        $block->setChild('section_social_sharing_bar', $socialSharingBarSection);
        $block->addSectionBlock('section_social_sharing_bar');

        $cmsBlockSection = Mage::app()->getLayout()->createBlock(
            'betterthankyoupage/sectionCmsblock', 'section_cms_block'
        );
        $cmsBlockSection->setTemplate('betterthankyoupage/section_cms_block.phtml');
        $block->setChild('section_cms_block', $cmsBlockSection);
        $block->addSectionBlock('section_cms_block');

        $crossSellsSection = Mage::app()->getLayout()->createBlock(
            'betterthankyoupage/sectionCrosssells', 'section_cross_sells'
        );
        $crossSellsSection->setTemplate('betterthankyoupage/section_cross_sells.phtml');
        $block->setChild('section_cross_sells', $crossSellsSection);
        $block->addSectionBlock('section_cross_sells');

        $newsletterSubscriptionSection = Mage::app()->getLayout()->createBlock(
            'betterthankyoupage/sectionNewslettersubscription', 'section_newsletter_subscription'
        );
        $newsletterSubscriptionSection->setTemplate('betterthankyoupage/section_newsletter_subscription.phtml');
        $block->setChild('section_newsletter_subscription', $newsletterSubscriptionSection);
        $block->addSectionBlock('section_newsletter_subscription');

        return $block;
    }
}