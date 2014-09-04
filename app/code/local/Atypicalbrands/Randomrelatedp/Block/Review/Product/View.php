<?php
class Atypicalbrands_Randomrelatedp_Block_Review_Product_View extends Mage_Review_Block_Product_View
{

	public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('product', $this->getProduct()->getId())
                ->setDateOrder();
        }
                $this->_reviewsCollection->getSelect()->order('rand()');
        return $this->_reviewsCollection;
    }

}
