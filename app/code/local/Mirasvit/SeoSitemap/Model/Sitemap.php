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


class Mirasvit_SeoSitemap_Model_Sitemap extends Mage_Sitemap_Model_Sitemap
{
    protected $io;
    protected $generateSitemapIndex;
    protected $currentLinks;
    protected $maxLinks;
    protected $sitemapNum;
    protected $splitSize;

    public function init($storeId) {
        $this->generateSitemapIndex = false;
        $this->currentLinks = 0;
        $this->maxLinks = Mage::getStoreConfig('sitemap/extended/max_links', $storeId);
        $this->sitemapNum = 0;
        $this->splitSize = Mage::getStoreConfig('sitemap/extended/split_size', $storeId);
    }

    public function getConfig() {
    	return Mage::getSingleton('seositemap/config');
    }

    public function openSitemap() {
        $this->io = new Varien_Io_File();
        $this->io->setAllowCreateFolders(true);
        $this->io->open(array('path' => $this->getPath()));

        $file = $this->getSitemapFilename($this->sitemapNum);

        if ($this->io->fileExists($file) && !$this->io->isWriteable($file)) {
            Mage::throwException(Mage::helper('sitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getSitemapFilename(), $this->getPath()));
        }

        $this->io->streamOpen($file);

        $this->io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $this->io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">');
    }

    public function closeSitemap() {
        $this->io->streamWrite('</urlset>');
        $this->io->streamClose();
    }

    public function getSitemapFilename($i = 0) {
        if ($i == 0) {
            return parent::getSitemapFilename();
        }
        $file = parent::getSitemapFilename();
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $fileNew = str_replace('.'.$ext, $i.'.'.$ext, $file);
        return $fileNew;
    }

    public function writeStream($xml) {
        $this->io->streamWrite($xml);
        $this->io->streamWrite("\n");
        $this->currentLinks++;
        if ($this->currentLinks == $this->maxLinks ||
            ($this->splitSize  > 0 && $this->io->streamStat('size') + 500 >= $this->splitSize*1024)) {
            if ($this->sitemapNum == 0) {
                $this->sitemapNum = 1;
                rename ($this->getPath().$this->getSitemapFilename(), $this->getPath().$this->getSitemapFilename($this->sitemapNum));
                $this->generateSitemapIndex = true;
            }
            $this->sitemapNum++;
            $this->currentLinks = 0;
            $this->closeSitemap();
            $this->openSitemap();
        }
    }

    /**
     * Generate XML file
     *
     * @return Mage_Sitemap_Model_Sitemap
     */

    public function delSlashUrl($noindex)
    {
        if ($noindex[0] == '/') $noindex=substr($noindex, 1);
        return $noindex;
    }

    public function generateXml()
    {
        $config = $this->getConfig();
        $storeId = $this->getStoreId();
        $this->init($storeId);
        Mage::app()->setCurrentStore($storeId);//need for correct URLs generation


        $date    = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);

        $this->openSitemap();
        /**
         * Generate categories sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/category/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/category/priority', $storeId);
        $collection = Mage::getResourceModel('sitemap/catalog_category')->getCollection($storeId);
        foreach ($collection as $item) {
            $chek_noindex = true;
            foreach (Mage::getModel('seo/config')->getNoindexPages() as $no_index)
            {
                if ($item->getUrl() == $this->delSlashUrl($no_index))
                {
                    $chek_noindex = false;
                    break;
                }
            }
            if ($chek_noindex != false)
            {
                $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                    htmlspecialchars($this->prepareUrl($baseUrl . $item->getUrl())),
                    $date,
                    $changefreq,
                    $priority
                );
                $this->writeStream($xml);
            }
        }
        unset($collection);

        /**
         * Generate products sitemap
         */
        $isAddProductImages   = Mage::getStoreConfig('sitemap/extended/is_add_product_images', $storeId);
        $changefreq = (string)Mage::getStoreConfig('sitemap/product/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/product/priority', $storeId);
        //~ $collection = Mage::getResourceModel('seositemap/catalog_product')->getCollection($storeId);
        //we need to load correct urls
        $i = 1;
        $step = 1000;
        do {
            $collection = Mage::getModel('catalog/product')->getCollection()
                                ->addAttributeToSelect('*')
                                ->addStoreFilter($storeId)
                                ->addAttributeToFilter('status', 1)
                                ->addAttributeToFilter('visibility', array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH))
                                ;
            $collection->getSelect()->limitPage($i,$step);
            $attribute = Mage::getSingleton('catalog/product')->getResource()->getAttribute('media_gallery');
            $media = Mage::getResourceSingleton('catalog/product_attribute_backend_media');
            $empty = true;
            foreach ($collection as $item) {
                $empty = false;
                $item->setStoreId($storeId);
                $images = '';
                if ($isAddProductImages) {
                    $gallery = $media->loadGallery($item, new Varien_Object(array('attribute' => $attribute)));
                    if (is_array($gallery)) {
                        foreach ($gallery as $image) {
                            if ($image['disabled'] == 1) {
                                continue;
                            }
                            $imageUrl = Mage::helper('mstcore/image')->init($item, 'thumbnail', 'catalog/product', $image['file']);
                            $imageUrl = str_replace('https://', 'http://', $imageUrl); //if backend has https://, we don't want to have https:// in images
                            $images .= '<image:image><image:loc>' . htmlspecialchars($imageUrl) . '</image:loc></image:image>';
                        }
                    }
                }
                $chek_noindex = true;
                foreach (Mage::getModel('seo/config')->getNoindexPages() as $no_index)
                {
                    if ($item->getProductUrl() == Mage::getBaseUrl('link').$this->delSlashUrl($no_index) ||
                        $item->getProductUrl() == Mage::getBaseUrl('web').$this->delSlashUrl($no_index))
                    {
                         $chek_noindex = false;
                         break;
                    }
                }
                if ($chek_noindex != false)
                {
                    $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority>%s</url>',
                                    htmlspecialchars($this->prepareUrl($item->getProductUrl())),
                                    $date,
                                    $changefreq,
                                    $priority,
                                    $images
                    );
                    $this->writeStream($xml);
                }
            }
            if ($empty) {
                break;
            }
            unset($collection);
            $i++;
        } while (true);
        /**
         * Generate cms pages sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/page/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/page/priority', $storeId);
        $ignore = $this->getConfig()->getIgnoreCmsPages();
        $collection = Mage::getModel('cms/page')->getCollection()
                         ->addStoreFilter($storeId)
                         ->addFieldToFilter('is_active', true)
                         ->addFieldToFilter('identifier', array('nin' => $ignore));
        foreach ($collection as $page) {
            $chek_noindex = true;
            foreach (Mage::getModel('seo/config')->getNoindexPages() as $no_index)
            {
                if ($page->getIdentifier() == $this->delSlashUrl($no_index))
                {
                     $chek_noindex = false;
                     break;
                }
            }
            if ($page->getIdentifier() != 'home' && $chek_noindex != false){
                $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                    htmlspecialchars($this->prepareUrl(Mage::getUrl(null, array('_direct' => $page->getIdentifier(), '_store' => $storeId)))),
                    $date,
                    $changefreq,
                    $priority
                );
               $this->writeStream($xml);
            }
        }
        unset($collection);

        if (Mage::getStoreConfig('sitemap/extended/is_add_product_tags', $storeId)) {
            $changefreq = (string)Mage::getStoreConfig('sitemap/extended/product_tags_changefreq', $storeId);
            $priority   = (string)Mage::getStoreConfig('sitemap/extended/product_tags_priority', $storeId);
            $collection = Mage::getModel('tag/tag')->getCollection()
                            ->addStoreFilter($storeId)
                            ->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)
                            ;
            foreach ($collection as $item) {
                $chek_noindex = true;
                foreach (Mage::getModel('seo/config')->getNoindexPages() as $no_index)
                {
                    if ($item->getTaggedProductsUrl() == Mage::getBaseUrl('link').$this->delSlashUrl($no_index)
                        || $item->getTaggedProductsUrl() == Mage::getBaseUrl('web').$this->delSlashUrl($no_index))
                    {
                         $chek_noindex = false;
                         break;
                    }
                }
                if ($chek_noindex != false)
                {
                    $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                        htmlspecialchars($this->prepareUrl($item->getTaggedProductsUrl())),
                        $date,
                        $changefreq,
                        $priority
                    );
                    $this->writeStream($xml);
                }
            }
            unset($collection);
        }

        $changefreq = (string)Mage::getStoreConfig('sitemap/extended/link_changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/extended/link_priority', $storeId);
        $links = $config->getAdditionalLinks();

        foreach ($links as $item) {
                $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                    htmlspecialchars($this->prepareUrl($baseUrl . ltrim($item->getUrl(), "/"))),
                    $date,
                    $changefreq,
                    $priority
                );
                $this->writeStream($xml);
        }
        unset($collection);

        Mage::dispatchEvent('sitemap_generate_action', array('sitemap' => $this));

        $this->closeSitemap();
        $this->generateSitemapIndex();
        $this->setSitemapTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
        $this->save();

        Mage::app()->setCurrentStore(0);//revert previous setting back to admin store
        return $this;
    }
    public function prepareUrl($url) {
        //clear URLs like
        //http://devstore.dva/index.php/htc-touch-diamond.html
        $url = str_replace('/index.php', '', $url);
        //clear URLs like
        //http://devstore2.dva/customer-service?SID=7e5o7l5cmrm6v48m96enb4oqk0
        $p = strpos($url, '?');
        if ($p !== false) {
            $url = substr($url, 0, $p);
        }
        //check for traling slash
        if (Mage::helper('mstcore')->isModuleInstalled('Mirasvit_Seo')) {
            $config = Mage::getSingleton('seo/config');
            $extension = substr(strrchr($url, '.'), 1);
            if (substr($url, -1) != '/' && $config->getTrailingSlash() == Mirasvit_Seo_Model_Config::TRAILING_SLASH) {
                if (!in_array($extension, array('html', 'htm', 'php', 'xml', 'rss'))) {
                   $url.= '/';
                }
            } elseif ($url != '/' && substr($url, -1) == '/' && $config->getTrailingSlash() == Mirasvit_Seo_Model_Config::NO_TRAILING_SLASH) {
               $url = rtrim($url, '/');
            }
        }
        return $url;
    }
    public function generateSitemapIndex() {
        if (!$this->generateSitemapIndex) {
            return;
        }
        if ($this->io->fileExists($this->getSitemapFilename()) && !$this->io->isWriteable($this->getSitemapFilename())) {
            Mage::throwException(Mage::helper('sitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getSitemapFilename(), $this->getPath()));
        }

        $this->io->streamOpen($this->getSitemapFilename());

        $this->io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $this->io->streamWrite('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

        $storeId = $this->getStoreId();
        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $store = Mage::app()->getStore($storeId);
        $baseUrl = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_DIRECT_LINK).ltrim($this->getSitemapPath(), '/');
        for ($i = 1; $i <= $this->sitemapNum; $i++) {

            if (file_exists($this->getPath() . $this->getSitemapFilename($i))) {
                $xml = sprintf('<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>',
                                htmlspecialchars($baseUrl . $this->getSitemapFilename($i)),
                                $date
                );
                $this->io->streamWrite($xml);
                $this->io->streamWrite("\n");
            }
        }

        $this->io->streamWrite('</sitemapindex>');
        $this->io->streamClose();
    }
}
