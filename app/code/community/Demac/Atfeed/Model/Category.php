<?php



class Demac_Atfeed_Model_Category extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('atfeed/category');
    }


    /**
     * @param $product_id
     * @param $rule_id
     * @internal param $customer_id
     * @internal param $rule_id
     * @return int
     */
    public function loadByCategoryName($name, $level = 0 )
    {
        $this->_getResource()->loadByCategoryName($this, $name, $level);
        return $this;
    }
}
