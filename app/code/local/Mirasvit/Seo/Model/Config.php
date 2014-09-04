<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.0.3
 * @revision  368
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Seo_Model_Config
{
    const NO_TRAILING_SLASH = 1;
    const TRAILING_SLASH = 2;

    const URL_FORMAT_SHORT = 1;
    const URL_FORMAT_LONG = 2;

	public function isAddCanonicalUrl()
	{
		return Mage::getStoreConfig('seo/general/is_add_canonical_url');
	}

	public function getCrossDomainStore()
	{
		return Mage::getStoreConfig('seo/general/crossdomain');
	}

	public function getTrailingSlash()
	{
        return Mage::getStoreConfig('seo/general/trailing_slash');
	}

	public function getCanonicalUrlIgnorePages()
	{
	    $pages = Mage::getStoreConfig('seo/general/canonical_url_ignore_pages');
	    $pages = explode("\n", trim($pages));
	    $pages = array_map('trim',$pages);

	    return $pages;
	}

	public function getNoindexPages()
	{
	    $pages = Mage::getStoreConfig('seo/general/noindex_pages');
	    $pages = explode("\n", trim($pages));
	    $pages = array_map('trim',$pages);

	    return $pages;
	}

	public function isEnabledSeoUrls()
	{
		return Mage::getStoreConfig('seo/general/layered_navigation_friendly_urls');
	}

	public function getProductUrlFormat()
	{
       return Mage::getStoreConfig('seo/general/product_url_format');
	}

	public function getProductUrlKey($store)
	{
       return Mage::getStoreConfig('seo/general/product_url_key', $store);
	}

	public function isPagingPrevNextEnabled()
	{
		return Mage::getStoreConfig('seo/general/is_paging_prevnext');
	}

	public function isOpenGraphEnabled()
	{
	    return Mage::getStoreConfig('seo/general/is_opengraph');
	}

	public function isRichSnippetsEnabled()
	{
	    return Mage::getStoreConfig('seo/general/is_rich_snippets');
	}
}
