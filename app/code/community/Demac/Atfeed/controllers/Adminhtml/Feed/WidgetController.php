<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Allan MacGregor - Magento Practice Lead <allan@demacmedia.com>
 * Company: Demac Media Inc.
 * Date: 6/1/13
 * Time: 12:00 PM
 */

class Demac_Atfeed_Adminhtml_Feed_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Prepare block for chooser
     *
     * @return void
     */
    public function chooserAction()
    {
        $request = $this->getRequest();

        switch ($request->getParam('attribute')) {
            case 'sku':
                $block = $this->getLayout()->createBlock(
                    'adminhtml/promo_widget_chooser_sku', 'promo_widget_chooser_sku',
                    array('js_form_object' => $request->getParam('form'),
                    ));
                break;

            case 'category_ids':
                $ids = $request->getParam('selected', array());
                if (is_array($ids)) {
                    foreach ($ids as $key => &$id) {
                        $id = (int) $id;
                        if ($id <= 0) {
                            unset($ids[$key]);
                        }
                    }

                    $ids = array_unique($ids);
                } else {
                    $ids = array();
                }


                $block = $this->getLayout()->createBlock(
                    'atfeed/adminhtml_feed_category_checkboxes_tree', 'feed_widget_chooser_category_ids',
                    array(
                        'js_form_object' => $request->getParam('form'),
                        'parent_id' => $request->getParam('parent_id')
                    )
                )
                    ->setCategoryIds($ids)
                ;
                break;

            default:
                $block = false;
                break;
        }

        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }

    public function categoriesAction()
    {
        echo 'test';
    }

}
