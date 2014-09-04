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


class Mirasvit_SeoSitemap_Model_Config
{
	public function getIsShowProducts()
	{
		return Mage::getStoreConfig('seo/seositemap/is_show_products');
	}

	public function getIsShowCmsPages()
	{
		return Mage::getStoreConfig('seo/seositemap/is_show_cms_pages');
	}

	public function getIgnoreCmsPages()
	{
		$conf = Mage::getStoreConfig('seo/seositemap/ignore_cms_pages');
		return explode(',', $conf);
	}

	public function getIsShowStores()
	{
		return Mage::getStoreConfig('seo/seositemap/is_show_stores');
	}

	public function getAdditionalLinks()
	{
		$conf = Mage::getStoreConfig('seo/seositemap/additional_links');
		$links = array();
		$ar = explode("\n", $conf);
		foreach ($ar as $v) {
		    $p = explode(',', $v);
		    if (isset($p[0]) && isset($p[1])) {
		        $links[] = new Varien_Object(array(
		        	'url' => trim($p[0]),
		        	'title' => trim($p[1])
		        ));
		    }
		}
		return $links;
	}
}