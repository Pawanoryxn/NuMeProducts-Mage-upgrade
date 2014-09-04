<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amacgregor
 * Date: 08/05/13
 * Time: 2:37 PM
 * To change this template use File | Settings | File Templates.
 */

class Demac_Atfeed_Adminhtml_FeedController extends Mage_Adminhtml_Controller_Action
{

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/atfeed')
            ->_addBreadcrumb(
                Mage::helper('atfeed')->__('AffiliateTraction Feed Manager'),
                Mage::helper('atfeed')->__('AffiliateTraction Feed Manager')
            )
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('AffiliateTraction Feed Manager'))->_title($this->__('AffiliateTraction Feed Manager'));
        $this->loadLayout();
        $this->_setActiveMenu('catalog/atfeed');
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('atfeed/feed');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('atfeed')->__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }

            $collection = Mage::getResourceModel('atfeed/feedattribute_collection')
                ->addFieldToFilter('feed_id', $id)
                ->load();

            foreach ($collection as $attribute) {
                $result[] = $attribute->getData();
            }
            
            Mage::register('attributes', $result);

        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $model->getActions()->setJsFormObject('rule_actions_fieldset');

        $this->_title($model->getId() ? $model->getName() : $this->__('New Feed'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::register('current_atfeed_feed', $model);

        $this->_initAction()->getLayout()->getBlock('atfeed_feed.edit')
            ->setData('action', $this->getUrl('*/*/save'));

        $this
            ->_addBreadcrumb(
                $id ? Mage::helper('atfeed')->__('Edit Feed')
                    : Mage::helper('atfeed')->__('New Feed'),
                $id ? Mage::helper('atfeed')->__('Edit Feed')
                    : Mage::helper('atfeed')->__('New Feed'))
            ->renderLayout();
    }

    /**
     * Promo quote save action
     */
    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('atfeed/feed');
                Mage::dispatchEvent(
                    'adminhtml_controller_atfeed_feed_prepare_save',
                    array('request' => $this->getRequest()));
                $data = $this->getRequest()->getPost();

                //filter HTML tags
                /** @var $helper Mage_Adminhtml_Helper_Data */
                $helper = Mage::helper('adminhtml');
                $data['name'] = $helper->stripTags($data['name']);
                $data['description'] = $helper->stripTags($data['description']);



                // $data = $this->_filterDates($data, array('from_date', 'to_date'));
                $id = $this->getRequest()->getParam('id');

                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('atfeed')->__('Wrong rule specified.'));
                    }
                }

                $session = Mage::getSingleton('adminhtml/session');

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }

                if (isset($data['simple_action']) && $data['simple_action'] == 'by_percent'
                    && isset($data['discount_amount'])) {
                    $data['discount_amount'] = min(100,$data['discount_amount']);
                }
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                unset($data['rule']);
                $model->loadPost($data);

                $session->setPageData($model->getData());

                $model->save();
                // TODO: Rewrite this to use transactions after model save
                $attributesCollection = Mage::getModel('atfeed/feedattribute')
                    ->getCollection()
                    ->addFieldToFilter('feed_id', $model->getId());

                foreach($attributesCollection as $attribute)
                {
                    $attribute->delete();
                }

                foreach($data['attributes'] as $attribute)
                {
                    if(!$attribute['delete']){
                        $feedAttribute = Mage::getModel('atfeed/feedattribute');
                        $feedAttribute->setFeedId($model->getId());
                        $feedAttribute->setAttributeId($attribute['attribute_id']);
                        $feedAttribute->setFeedAttribute($attribute['feed_attribute']);
                        $feedAttribute->save();
                    }
                }
                // End Rewrite

                $session->addSuccess(Mage::helper('atfeed')->__('The feed has been saved.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }

                if ($this->getRequest()->getParam('generate')) {
                    $this->_redirect('*/*/generate', array('id' => $model->getId()));
                    return;
                }

                $this->_redirect('*/*/');
                    return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('atfeed')->__('An error occurred while saving the feed data. Please review the log and try again.'));
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function generateAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('atfeed/feed');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('atfeed')->__('This feed no longer exists.'));
                $this->_redirect('*/*');
                return;
            }

            $collection = Mage::getResourceModel('atfeed/feedattribute_collection')
                ->addFieldToFilter('feed_id', $id)
                ->load();

            foreach ($collection as $attribute) {
                $attributes[] = $attribute->getData();
            }
            $generator = Mage::getModel('atfeed/generator')->initialize($model, $attributes);

            try {
                $generator->generateTabDelimitedFile();
                $generator->uploadFeed();
                $generator->setRuleStatus(1);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('atfeed')->__('The feed has been succesfuly generated and uploaded.'));

            } catch (Exception $e) {
                $generator->setRuleStatus(0);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('atfeed')->__('There was a problem generating the feed, please check the log for more details.'));
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
            }
            $this->_redirect('*/*/');

            return $this;

        }

        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('atfeed')->__('Unable to find a feed to Generate.'));
        $this->_redirect('*/*/');



    }

    /**
     *
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('atfeed/feed');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('atfeed')->__('The feed has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('catalogrule')->__('An error occurred while deleting the feed. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('atfeed')->__('Unable to find a feed to delete.'));
        $this->_redirect('*/*/');
    }

}