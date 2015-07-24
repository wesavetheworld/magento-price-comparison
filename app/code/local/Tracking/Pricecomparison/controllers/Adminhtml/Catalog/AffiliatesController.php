<?php
/**
 * Copyright 2015 Tjard Henrik Kügler
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
 
/**
 * Admincontroller to manage the affiliates and prices
 * connected to this modul.
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Adminhtml_Catalog_AffiliatesController extends Mage_Adminhtml_Controller_Action
{
	/**
     * Check for is allowed
     *
     * @return boolean
     */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('catalog/pricecomparison_affiliates');
	}
	
	/**
     * Controller predispatch method
     *
     * @return Tracking_Pricecomparison_Adminhtml_Catalog_AffiliatesController
     */
	public function preDispatch()
	{
		parent::preDispatch();
		
		if (!Mage::helper('pricecomparison/adminhtml_data')->isEnabled()) {
			$this->setFlag('', 'no-dispatch', true);
			$this->_redirect('adminhtml/dashboard');
		}
		
		return $this;
	}
	
	/**
	 * Initialize this controller.
	 * 
	 * @return Tracking_Pricecomparison_Adminhtml_Catalog_AffiliatesController
	 */
	protected function _initAction()
	{
		$helper = Mage::helper('pricecomparison/adminhtml_data');
		
		$this->loadLayout()
			->_setActiveMenu('catalog/pricecomparison_affiliates')
			->_addBreadcrumb(
				$helper->__('Catalog'),
				$helper->__('Catalog')
			  )
			->_addBreadcrumb(
				$helper->__('Manage Affiliates'),
				$helper->__('Manage Affiliates')
			  )
		;
		return $this;
	}
	
	/**
	 * Basic controller action
	 */
	public function indexAction()
	{
		$this->loadLayout();
		
		$this->_title('Manage Affiliates');
		$this->_setActiveMenu('catalog/pricecomparison_affiliates');
		$this->renderLayout();
	}
	
	/**
	 * Add action.
	 * Will be called from form.
	 * 
	 * @return Tracking_Pricecomparison_Adminhtml_Catalog_AffiliatesController
	 */
	public function addAction()
	{
		$params = $this->getRequest()->getPost();
		try {
			/* @var $affiliates Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates */ 
			$affiliates = Mage::getModel('pricecomparison/adminhtml_catalog_affiliates');
			$affiliates->addAffiliate($params);
		}
		catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            Mage::logException($e);
		}
		
		return $this->_redirect('*/*/index', array('active_tab' => 'add'));
	}

	/**
	 * Checks if a given filename has a valid extension.
	 * The list of valid extensions is 'hard-coded' here
	 * in $validExtensions.
	 * 
	 * Probably can be improved, if needed.
	 * 
	 * @var string $filename
	 * @throws Mage_Core_Exception
	 */
	protected function _isValidFileExtension($fileName)
	{
		$validExtensions = array(
			'csv',
		);
		
		$explodedName = explode('.', $fileName);
		$extension = strtolower(end($explodedName));
		
		if (!in_array($extension, $validExtensions)) {
			Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('Invalid file format ".%s".', $extension));
		}
	}
	
	/**
	 * Import action.
	 * 
	 * @return Tracking_Pricecomparison_Adminhtml_Catalog_AffiliatesController
	 */
	public function importAction()
	{
		/* @var $session Mage_Adminhtml_Model_Session */
		$session = Mage::getSingleton('adminhtml/session');
		
		if (!empty($_FILES) && array_key_exists('import', $_FILES) && $_FILES['import']['error']['file'] == UPLOAD_ERR_OK) {
			$file = $_FILES['import'];
			$fileName = $file['name']['file'];		
			$path = $file['tmp_name']['file'];
		
			try {
				$this->_isValidFileExtension($fileName);
				/* @var $import Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Import */
				$import = Mage::getModel('pricecomparison/adminhtml_catalog_affiliates_import', $path);
				$params = $this->getRequest()->getPost();
				$import->startImport($params['import']['select']);
				$session->addSuccess(Mage::helper('pricecomparison/adminhtml_data')->__('Data has been imported.'));
			}
			catch (Exception $e) {
				$session->addError($e->getMessage());
				Mage::logException($e);
			}
		}
		else {
			$session->addError('There has been an error processing your file.');
		}
		return $this->_redirect('*/*/index', array('active_tab' => 'import'));
	}
	
	/**
     * Export Action.
     *
     * @return Tracking_Pricecomparison_Adminhtml_Catalog_AffiliatesController
     */
    public function exportAction()
    {
        if ($post = $this->getRequest()->getPost('export')) {
            try {
                /** @var $model Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Export */
                $model = Mage::getModel('pricecomparison/adminhtml_catalog_affiliates_export');
                $model->startExport($post['select']);

                return $this->_prepareDownloadResponse(
                    $model->getFileName(),
                    $model->getContents(),
                    $model->getContentType()
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('No valid data sent'));
            }
        } else {
            $this->_getSession()->addError($this->__('No valid data sent'));
        }
        return $this->_redirect('*/*/index', array('active_tab' => 'export'));
    }
}
