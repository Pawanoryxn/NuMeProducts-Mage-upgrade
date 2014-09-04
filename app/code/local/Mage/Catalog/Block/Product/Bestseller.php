<?php 
class Mage_Catalog_Block_Product_Bestseller extends Mage_Catalog_Block_Product_Abstract
    {

    protected function _construct()
    {
        $this->addData(array(
        'cache_key' => 'bestseller_settocache',
        'cache_lifetime'    => 999999999,
        'cache_tags'        => array(
            Mage_Core_Model_Store::CACHE_TAG,
            Mage_Cms_Model_Block::CACHE_TAG),
        )
        );
    } 

    /*public function __construct(){  
        parent::__construct();
        $storeId = Mage::app()->getStore()->getId();
        $products = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->addAttributeToSelect('*')
            ->addAttributeToSelect(array('name', 'price', 'small_image'))
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->setOrder('ordered_qty', 'desc'); // most best sellers on top
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
 
        $products->setPageSize(4)->setCurPage(1);
        $this->setProductCollection($products);
    }*/

    public function getBestSellersLast24Hours()
    {
        $timePeriod = 1;
        $BestSellerIDs = array();
        $excludecategory1 = 43;
        $excludecategory2 = 38;
        $currentStoreID = Mage::app()->getStore()->getStoreId();
        $date = date('Y-m-d H:i:s');
        $newdate = strtotime ( '-'.$timePeriod.' day' , strtotime ( $date ) ) ;
        $newdate = date ( 'Y-m-d' , $newdate );
        $newdate = $newdate.date(' H:i:s');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $table_prefixx = Mage::getConfig()->getTablePrefix();
        $res = $write->query("select product_id from ".$table_prefixx."sales_flat_order_item WHERE (created_at<'".$date."' AND created_at>'".$newdate."') AND product_type!='simple' AND store_id=".$currentStoreID." Group By `product_id` ORDER BY SUM(qty_ordered) DESC");
        foreach($res->fetchall() as $value)
        $BestSellerIDs[] = $value['product_id']; 
        $count = 0;
        foreach($BestSellerIDs as $pids):
        $_product = Mage::getModel('catalog/product')->load($pids);
        $ids = $_product->getCategoryIds();
        if($count < 4):
        if(!in_array($excludecategory1,$ids) AND !in_array($excludecategory2,$ids)):
        $BestSellerIDsReturn[] = $pids;
        $count = $count +1;
        endif;
        endif;
        endforeach;
        if(sizeof($BestSellerIDsReturn) > 0):
        return $BestSellerIDsReturn;
        else:
        return FALSE;
        endif;
    }

}
?>