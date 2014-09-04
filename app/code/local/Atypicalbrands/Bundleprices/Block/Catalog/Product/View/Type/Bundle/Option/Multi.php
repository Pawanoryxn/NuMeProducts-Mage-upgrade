<?php

class Atypicalbrands_Bundleprices_Block_Catalog_Product_View_Type_Bundle_Option_Multi
    extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Multi
{
	public function getSelectionTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        $tierPrice = $_selection->getTierPrice();
        if (!empty($tierPrice)) {
            $qty = $_selection->getSelectionQty();
            $price = $qty * (float) $_selection->getPriceModel()->getTierPrice($qty, $_selection);
        }
        $this->setFormatProduct($_selection);
        $priceTitle = $this->escapeHtml($_selection->getName());
        if (strpos($this->formatPriceString($price, $includeContainer), "0.00") != 1){
            $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
                . '+' . $this->formatPriceString($price, $includeContainer)
                . ($includeContainer ? '</span>' : '');
        }
        return $priceTitle;
    }
}
