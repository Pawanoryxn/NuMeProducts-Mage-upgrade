<?php

class Moogento_ShipEasy_Block_Adminhtml_Sales_Order_Grid_Comment extends Mage_Adminhtml_Block_Template
{
    const XML_PATH_TRUNCATE = 'moogento_shipeasy/grid/admin_comments_truncate';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('moogento/sales/order/grid/comment.phtml');
    }

    protected function _getTruncatedComment($comment)
    {
        if ($truncate = Mage::getStoreConfig(self::XML_PATH_TRUNCATE)) {
            if ($truncate < strlen($comment)) {
                return substr($comment, 0, $truncate) . '...'; 
            }
        }
        return $comment;
    }
}