<?php

class Moogento_ShipEasy_Adminhtml_System_Convert_ShipmentsController
    extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
        ->_setActiveMenu('system/convert')
        ->_addBreadcrumb($this->__('System'), $this->__('System'))
        ->_addBreadcrumb($this->__('Import Shipments'), $this->__('Import Shipments'));

        $this->_title($this->__('System'))->_title($this->__('Import Shipments'));
        
        $this->renderLayout();
    }

    public function postAction()
    {
        if (isset($_FILES['import_file']['tmp_name'])) {
            if ($file = $_FILES['import_file']['tmp_name']) {
                try {
                    $uploader = new Varien_File_Uploader('import_file');
                    $uploader->setAllowedExtensions(array('csv'));
                    $path = Mage::app()->getConfig()->getTempVarDir().'/import/';
                    $uploader->save($path);
                    if ($uploadFile = $uploader->getUploadedFileName()) {
                        $newFilename = 'import-'.date('YmdHis').'-track_import_'.$uploadFile;
                        rename($path.$uploadFile, $path.$newFilename);
                    }
                } catch(Exception $e) {
                    $this->_getSession()->addError($this->__($e->getMessage()));
                    $this->_redirect('*/*/index');
                    return;
                }
            }
            if (isset($newFilename) && $newFilename) {
                $contents = file_get_contents($path.$newFilename);
                if (ord($contents[0]) == 0xEF && ord($contents[1]) == 0xBB && ord($contents[2]) == 0xBF) {
                    $contents = substr($contents, 3);
                    file_put_contents($path.$newFilename, $contents);
                }
                unset($contents);
            }
            
            $profile = Mage::helper('moogento_shipeasy/track_import')
                ->getImportProfile(
                    'var/import',
                    $newFilename
                );

            /**
             * Dummy id to pass ID checks
             */
            $profile->setId(1000);
            Mage::register('current_convert_profile', $profile);

            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_getSession()->addError(Mage::helper('moogento_shipeasy')->__('Can not find import file'));
            $this->_redirect('*/*/index');
        }
    }

    public function batchRunAction()
    {
        if ($this->getRequest()->isPost()) {
            $batchId = $this->getRequest()->getPost('batch_id',0);
            $rowIds  = $this->getRequest()->getPost('rows');

            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);
            /* @var $batchModel Mage_Dataflow_Model_Batch */

            if (!$batchModel->getId()) {
                //exit
                return ;
            }
            if (!is_array($rowIds) || count($rowIds) < 1) {
                //exit
                return ;
            }
            if (!$batchModel->getAdapter()) {
                //exit
                return ;
            }

            $batchImportModel = $batchModel->getBatchImportModel();
            $importIds = $batchImportModel->getIdCollection();

            $adapter = Mage::getModel($batchModel->getAdapter());
            $adapter->setBatchParams($batchModel->getParams());

            $errors = array();
            $saved  = 0;

            foreach ($rowIds as $importId) {
                $batchImportModel->load($importId);
                if (!$batchImportModel->getId()) {
                    $errors[] = Mage::helper('dataflow')->__('Skip undefined row.');
                    continue;
                }

                try {
                    $importData = $batchImportModel->getBatchData();
                    $adapter->saveRow($importData);
                }
                catch (Exception $e) {
                    $errors[] = $e->getMessage();
                    continue;
                }
                $saved ++;
            }

            $result = array(
                'savedRows' => $saved,
                'errors'    => $errors
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function batchFinishAction()
    {
        if ($batchId = $this->getRequest()->getParam('id')) {
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);
            /* @var $batchModel Mage_Dataflow_Model_Batch */

            if ($batchModel->getId()) {
                $result = array();
                try {
                    $batchModel->beforeFinish();
                }
                catch (Mage_Core_Exception $e) {
                    $result['error'] = $e->getMessage();
                }
                catch (Exception $e) {
                    $result['error'] = Mage::helper('adminhtml')->__('An error occurred while finishing process. Please refresh the cache');
                }
                $batchModel->delete();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }    
}