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


class Mirasvit_Seo_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $config;

    public function __construct()
    {
        $this->_config = Mage::getModel('seo/config');
    }

    public function getBaseUri()
    {
        $baseStoreUri = parse_url(Mage::getUrl(), PHP_URL_PATH);

        if ($baseStoreUri  == '/') {
            return $_SERVER['REQUEST_URI'];
        } else {
            $requestUri = $_SERVER['REQUEST_URI'];
            $prepareUri = str_replace($baseStoreUri, '', $requestUri);
            if (substr($requestUri, 0, 1) == '/') {
                return $prepareUri;
            } else {
                return DS . $prepareUri;
            }
        }
    }

    protected function checkRewrite()
    {
        Mage::helper('mstcore/debug')->start();

        $uri = $this->getBaseUri();
        $collection = Mage::getModel('seo/rewrite')->getCollection()
            ->addStoreFilter(Mage::app()->getStore())
            ->addEnableFilter();
        $resultRewrite = false;
        foreach ($collection as $rewrite) {
            if ($this->checkPattern($uri, $rewrite->getUrl())) {
                $resultRewrite = $rewrite;
                break;
            }
        }

        Mage::helper('mstcore/debug')->end(array(
            'uri'         => $uri,
            'rewrite_id'  => $resultRewrite? $resultRewrite->getId() : false,
            'rewrite_url' => $resultRewrite? $resultRewrite->getUrl() : false,
        ));

        return $resultRewrite;
    }

    /**
     * ÐÐ¾Ð·Ð²ÑÐ°ÑÐ°ÐµÑ ÑÐµÐ¾-Ð´Ð°Ð½Ð½ÑÐµ Ð´Ð»Ñ ÑÐµÐºÑÑÐµÐ¹ ÑÑÑÐ°Ð½Ð¸ÑÑ
     *
     * ÐÐ¾Ð·Ð²ÑÐ°ÑÐ°ÐµÑ Ð¾Ð±ÑÐµÐºÑ Ñ Ð¼ÐµÑÐ¾Ð´Ð°Ð¼Ð¸:
     * getTitle() - Ð·Ð°Ð³Ð¾Ð»Ð¾Ð²Ð¾Ðº H1
     * getDescription() - SEO ÑÐµÐºÑÑ
     * getMetaTitle()
     * getMetaKeyword()
     * getMetaDescription()
     *
     * ÐÑÐ»Ð¸ Ð´Ð»Ñ Ð´Ð°Ð½Ð½Ð¾Ð¹ ÑÑÑÐ°Ð½Ð¸ÑÑ Ð½ÐµÑ Ð¡ÐÐ, ÑÐ¾ Ð²Ð¾Ð·Ð²ÑÐ°ÑÐ°ÐµÑ Ð¿ÑÑÑÐ¾Ð¹ Varien_Object
     *
     * @return Varien_Object $result
     */
    public function getCurrentSeo()
    {
        if (Mage::app()->getStore()->getCode() == 'admin') {
            return new Varien_Object();
        }

        Mage::helper('mstcore/debug')->start();

        $isCategory = Mage::registry('current_category') || Mage::registry('category');

        if ($isCategory) {
            $filters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
            $isFilter = count($filters) > 0;
        }

        if (Mage::registry('current_product') || Mage::registry('product')) {
            $seo = Mage::getSingleton('seo/object_product');
        } elseif ($isCategory && $isFilter) {
            $seo =  Mage::getSingleton('seo/object_filter');
        } elseif ($isCategory) {
            $seo =  Mage::getSingleton('seo/object_category');
        } else {
            $seo = new Varien_Object();
        }

        if ($seoRewrite = $this->checkRewrite()) {
            foreach ($seoRewrite->getData() as $k=>$v) {
                if ($v) {
                   $seo->setData($k, $v);
                }
            }
        }

        if (Mage::registry('current_category')) {
            $page = Mage::app()->getFrontController()->getRequest()->getParam('p');
            if ($page > 1) {
                $seo->setMetaTitle(Mage::helper('seo')->__("Page %s | %s", $page, $seo->getMetaTitle()));
                $seo->setDescription('');
            }
        }

        Mage::helper('mstcore/debug')->end($seo->getData());

        return $seo;
    }

    public function checkPattern($string, $pattern, $caseSensative = false)
    {
        if (!$caseSensative) {
            $string  = strtolower($string);
            $pattern = strtolower($pattern);
        }

        $parts = explode('*', $pattern);
        $index = 0;

        $shouldBeFirst = true;
        $shouldBeLast  = true;

        foreach ($parts as $part) {
            if ($part == '') {
                $shouldBeFirst = false;
                continue;
            }

            $index = strpos($string, $part, $index);

            if ($index === false) {
                return false;
            }

            if ($shouldBeFirst && $index > 0) {
                return false;
            }

            $shouldBeFirst = false;
            $index += strlen($part);
        }

        if (count($parts) == 1) {
            return $string == $pattern;
        }

        $last = end($parts);
        if ($last == '') {
            return true;
        }

        if (strrpos($string, $last) === false) {
            return false;
        }

        if(strlen($string) - strlen($last) - strrpos($string, $last) > 0) {
          return false;
        }

        return true;
    }

	public function cleanMetaTag($tag) {
        $tag = strip_tags($tag);
        //$tag = html_entity_decode($tag);//for case we have tags like &nbsp; added by some extensions //in some hosting adds unrecognized symbols
        //$tag = preg_replace('/[^a-zA-Z0-9_ \-()\/%-&]/s', '', $tag);
        $tag = preg_replace('/\s{2,}/', ' ', $tag); //remove unnecessary spaces
        $tag = preg_replace('/\"/', ' ', $tag); //remove " because it destroys html
        $tag = trim($tag);

	    return $tag;
	}
}
