<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Allan MacGregor - Magento Practice Lead <allan@demacmedia.com>
 * Company: Demac Media Inc.
 * Date: 5/29/13
 * Time: 11:12 AM
 */

class Demac_Atfeed_Model_Generator extends Mage_Core_Model_Abstract
{

    protected $file_path = '';
    protected $total_records = 0;
    protected $generated_records = 0;
    protected $errors = array();
    protected $start_time = 0;
    protected $_productCollection;
    protected $_attributeColumns = array();
    protected $_attributes = array();
    protected $_attributeMap= array();

    protected $stopped = false;
    protected $started = false;
    protected $finished = false;


    protected $_rule = false;

    public function _construct() {
        parent::_construct();
        $this->_init('atfeed/generator');

        $this->_attributeColumns = array('id','title','description','google_product_category','product_type',
            'link','image_link','additional_image_link','condition','availability','price','sale_price',
            'sale_price_effective_date','brand','gtin','mpn','gender','age_group','color','size',
            'item_group_id','material','pattern','shipping','metakeywords');
    }

    /**
     * Return real file path
     *
     * @return string
     */
    protected function getPath()
    {
        if (is_null($this->_filePath)) {
            $this->_filePath = str_replace('//', '/', Mage::getBaseDir() . '/var/demac_atfeed/');
                //$this->getFeedPath());
        }
        return $this->_filePath;
    }

    public function getFeedPath()
    {
        return $this->_rule->getFtpPath();
    }

    public function getFeedFilename()
    {
        return 'dmm_' . $this->_rule->getFtpFilename();
    }

    public function setRuleStatus($status)
    {
        $this->_rule->setStatus($status)->save();
    }

    public function initialize($feedRule,$attributes)
    {

        $this->_attributes = $attributes;
        $this->_rule = $feedRule;

        foreach($attributes as $attribute)
        {
            $loadedAttribute = Mage::getModel('eav/entity_attribute')->load($attribute['attribute_id']);
            $this->_attributeMap[$attribute['feed_attribute']] = $loadedAttribute->getAttributeCode();
        }

        $this->_productCollection = $feedRule->getProductCollection($attributes);

        // check for file existance and unlink it
        $io = new Varien_Io_File();
        $realPath = $io->getCleanPath($this->getPath());

        /**
         * Check path is allow
         */
        if (!$io->allowedPath($realPath, Mage::getBaseDir())) {
            Mage::throwException(Mage::helper('atfeed')->__('Please define correct path'));
        }
        /**
         * Check exists and writeable path
         */
        if (!$io->fileExists($realPath, false)) {
            Mage::throwException(Mage::helper('atfeed')->__('Please create the specified folder "%s" before saving the sitemap.', Mage::helper('core')->htmlEscape($this->getPath())));
        }

        if (!$io->isWriteable($realPath)) {
            Mage::throwException(Mage::helper('atfeed')->__('Please make sure that "%s" is writable by web-server.', $this->getPath()));
        }

        /**
         * Check allow filename
         */
        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getFeedFilename())) {
            Mage::throwException(Mage::helper('atfeed')->__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'));
        }
        if (!preg_match('#\.txt#', $this->getFeedFilename())) {
            $this->setFeedFilename($this->getFeedFilename() . '.txt');
        }

        return $this;
    }

    public function generateTabDelimitedFile()
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));

        if ($io->fileExists($this->getFeedFilename()) && !$io->isWriteable($this->getFeedFilename())) {
            Mage::throwException(Mage::helper('atfeed')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getFeedFilename(), $this->getPath()));
        }

        $io->streamOpen($this->getFeedFilename());

        $storeId = $this->getStoreId();
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);

        $columns = implode("\t",$this->_attributeColumns);
        $io->streamWrite($columns."\r\n");

        $collection = $this->_productCollection;
        foreach ($collection as $item) {
            $line = $this->generateRow($item);
            $io->streamWrite($line);
        }
        unset($collection);
        $io->streamClose();

        $this->_rule->setGeneratedAt(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));

        return $this;
    }

    public function uploadFeed()
    {
        $filename       = $this->getFeedFilename();

        $ftpServer      = $this->_rule->getFtpHost();
        $ftpUsername    = $this->_rule->getFtpUser();
        $ftpPassword    = $this->_rule->getFtpPass();
        $ftpPath        = $this->_rule->getFtpPath() . $filename;
        $localPath      = Mage::getBaseDir() . '/var/demac_atfeed/' . $filename;

        $conn_id = ftp_connect($ftpServer, 21);

        // login with username and password
        $login_result = ftp_login($conn_id, $ftpUsername, $ftpPassword);
        if ((!$conn_id) || (!$login_result)) {
            Mage::throwException($this->__('There was an error connecting to the server, please review your credentials.'));
        }

        ftp_pasv($conn_id, true);
        $upload = ftp_put($conn_id, $ftpPath, $localPath, FTP_ASCII);

        if(!$upload)
        {
            Mage::throwException($this->__('There was an error uploading the feed file'));
            return $this;
        }

        $this->_rule->setUploadedAt(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));

        return $this;
    }


    protected function _cleanText($text){
        $text = '"'. str_replace("\"",'\"',$text) . '"';
        return $text;
    }

    protected function _getCategoryPath($categoryId)
    {
        $googleCat = Mage::getModel('atfeed/category')->load($categoryId);
        return $googleCat->getCategoryPath();
    }

    protected function _getMagentoCategory($product)
    {
        $_categories = $product->getCategoryIds();
        $paths = array();

//        $url = $this->getUrl($_category->getUrlPath()).basename($product->getProductUrl());

        foreach($_categories as $_category)
        {
            $cPath = array();

            $_category = Mage::getModel('catalog/category')->load($_category);
            $categoryIds = explode('/',$_category->getPath());

            foreach($categoryIds as $categoryId)
            {
                if($categoryId > 2){
                    $_category = Mage::getModel('catalog/category')->load($categoryId);
                    $cPath[] = $_category->getName();
                }
            }

            $paths[] = implode(' > ', $cPath);

        }

        return implode(',',$paths);
    }

    protected function _getGoogleCategory($product)
    {
        $category = false;
        $ruleCondition = $this->_rule->getConditions();
        foreach($ruleCondition['conditions'] as $condition)
        {
            $object = new Varien_Object();
            $object->setProduct($product);
            if($condition->validate($object))
            {
                $category = $condition->getFeedcategory();
            }
        }

        if($category)
        {
            $category = $this->_getCategoryPath($category);
        }

        return $category;
    }

    protected function generateRow($product)
    {
        $product->setStoreId($this->_rule->getStoreId())->load();
        $row = '';

        $googleCat  = $this->_getGoogleCategory($product);
        $magentoCat = $this->_getMagentoCategory($product);

        $images = array();

        foreach ($product->getMediaGalleryImages()->getItems() as $image) {
            $images[] = $image->getUrl();
        }

        $mainImage = (array_key_exists(0, $images)) ? $images[0] : "";
        $additionalImage = (array_key_exists(1, $images)) ? $images[1] : "";

        // mapping the feed attributes

        if(Mage::getStoreConfig('sales/atfeed/simple_mode')) {
            if ($product->getTypeId() == 'configurable') {
                $childProducts = Mage::getModel('catalog/product_type_configurable')
                    ->getUsedProducts(null, $product);

                foreach ($childProducts as $child) {
                    $child->setStoreId($this->_rule->getStoreId())->load();
                    if ($child->isInStock()) {
                        $rawRow = $this->_prepareRowData($child, $mainImage, $googleCat, $magentoCat, $product->getProductUrl(), $mainImage, $additionalImage, $product->getMetaKeyword(), $product->getId()) . "\r\n";
                    }
                }
            } else {
                $row .= $this->_prepareRowData($product, $mainImage, $googleCat, $magentoCat, $product->getProductUrl(), $mainImage, $additionalImage, $product->getMetaKeyword()) . "\r\n";
            }
        } else {
            if ($product->getTypeId() == 'configurable') {
                $childProducts = Mage::getModel('catalog/product_type_configurable')
                    ->getUsedProducts(null, $product);
                $childAttributes = $this->getChildProductsValues($childProducts);
                $row = $this->_prepareRowData($product, $mainImage, $googleCat, $magentoCat, $product->getProductUrl(), $mainImage, $additionalImage, $product->getMetaKeyword(),
                            false ,$childAttributes['color'],$childAttributes['size'],$childAttributes['material'],$childAttributes['pattern']) . "\r\n";


            }

        }

        return $row;

    }

    protected function getChildProductsValues($childProducts)
    {
        $attributes = array();
        foreach ($childProducts as $product) {
            $attributes['color'][] = $this->_getMappedAttributeValue('color', $product);
            $attributes['size'][]  = $this->_getMappedAttributeValue('size', $product);
            $attributes['material'][]  = $this->_getMappedAttributeValue('material', $product);
            $attributes['pattern'][]  = $this->_getMappedAttributeValue('pattern', $product);
        }

        foreach($attributes as $key => $value)
        {
            $value = array_unique(array_filter($value));

            if (!empty($value)) {
                $attributes[$key] = implode(',', $value);
            }else{
                $attributes[$key] = '';
            }
        }

        return $attributes;

    }
    

    protected function _prepareRowData($product,$mainImage,$googleCat,$magentoCat,$parentUrl,$mainImage,$additionalImage,$metakeyword = '',$parentId = false, $color = null, $size = null, $material = null, $pattern = null)
    {
        if ($product->getTypeId() == 'simple') {
            if ($product->isInStock()) {
                $availability = 'in stock';
            } else {
                $availability = 'out of stock';
            }
        } else {
            $stockItem = $product->getStockItem();
            if($stockItem->getIsInStock())
            {
                $availability = 'in stock';
            }
            else
            {
                $availability = 'out of stock';
            }
        }

        $now = new DateTime();
        $now->format('Y-m-d H:i:s');    // MySQL datetime format

        $specialprice = $product->getSpecialPrice();
        $specialPriceFromDate =  $product->getSpecialFromDate();
        $specialPriceToDate =  $product->getSpecialToDate();
        // Get Current date
        $today =  time();

        if ($specialprice)
        {
            if($today >= strtotime( $specialPriceFromDate)
                && $today <= strtotime($specialPriceToDate)
                || $today >= strtotime( $specialPriceFromDate)
                && is_null($specialPriceToDate))
            {
                $price = $product->getPrice();
                $salePrice = $product->getSpecialPrice();
                $salesDate = $product->getSpecialFromDate();
            } else {
                $price = $product->getPrice();
                $salePrice = $product->getPrice();
                $salesDate = '';
            }
        }else {
            $price = $product->getPrice();
            $salePrice = $product->getPrice();
            $salesDate = '';
        }

        $metakeyword .= $product->getMetaKeyword();

        $title = $this->_getMappedAttributeValue('title', $product);
        $content = '"'. trim(preg_replace( '/\s+/', ' ',   $this->_getMappedAttributeValue('content', $product))) .'"';
        $brand = $this->_getMappedAttributeValue('brand', $product);
        $gtin = $this->_getMappedAttributeValue('gtin', $product);
        $mpn = $this->_getMappedAttributeValue('mpn', $product);

        if (is_null($color)) {
            $color = $this->_getMappedAttributeValue('color', $product);
        }
        if (is_null($size)) {
            $size = $this->_getMappedAttributeValue('size', $product);
        }
        if (is_null($material)) {
            $material = $this->_getMappedAttributeValue('material', $product);
        }
        if (is_null($pattern)) {
            $pattern = $this->_getMappedAttributeValue('pattern', $product);
        }

        $gender = $this->_getMappedAttributeValue('gender', $product);
        $ageGroup = $this->_getMappedAttributeValue('age_group', $product);

        $gender = ($gender != '') ? $gender : "unisex";
        $ageGroup = ($ageGroup != '') ? $ageGroup : "adult";

        $row = new Varien_Object();

        $row->setId($product->getId()); //system
        $row->setTitle($title); //dynamic
        $row->setDescription($content); //dynamic
        $row->setGoogleProductCategory($googleCat); //dynamic
        $row->setProductType($magentoCat); // system
        $row->setLink($parentUrl); // system
        $row->setImageLink($mainImage); //system
        $row->setAdditionalImageLink($additionalImage); //system
        $row->setCondition('New'); //harcoded
        $row->setAvailability($availability); //system
        $row->setPrice($price); //system
        $row->setSalePrice($salePrice); //system
        $row->setSalePriceEffectiveDate($salesDate); //system
        $row->setBrand($brand); //dynamic
        $row->setGtin($gtin); //dynamic
        $row->setMpn($mpn); //dynamic
        $row->setGender($gender); //harcoded
        $row->setAgeGroup($ageGroup); //harcoded
        $row->setColor($color); //dynamic
        $row->setSize($size); //dynamic
        $row->setItemGroupId(); //system
        $row->setMaterial($material); //dynamic
        $row->setPattern($pattern); //dynamic
        $row->setShipping('');

        $breaks = array("\r\n", "\n", "\r");
        $metakeyword = str_replace($breaks, "", $metakeyword);

        $row->setMetakeywords($metakeyword);

        return implode("\t",$row->__toArray());
    }

    protected function _getMappedAttributeValue($attributeCode,$product)
    {
        return (array_key_exists($attributeCode, $this->_attributeMap)) ? $product->getResource()->getAttribute($this->_attributeMap[$attributeCode])->getFrontend()->getValue($product)  : "";
    }


}