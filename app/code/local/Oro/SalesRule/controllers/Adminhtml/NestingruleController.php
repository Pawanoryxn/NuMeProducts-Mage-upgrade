<?php
/**
 * @category   Oro
 * @package    Oro_SalesRule
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Controller to render rules chooser grid
 */
class Oro_SalesRule_Adminhtml_NestingruleController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Grid ajax action in chooser mode
     */
    public function chooserGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
