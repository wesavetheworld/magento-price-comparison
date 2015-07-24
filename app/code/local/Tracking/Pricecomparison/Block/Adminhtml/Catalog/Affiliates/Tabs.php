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
 * Tabs of for 'Manage Affiliates' menu.
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
        $this->setId('tabs');
		$this->setDestElementId('pricecomparison_container');
        $this->setTitle(Mage::helper('pricecomparison')->__('Price Comparison'));
	}
	
	/**
	 * Prepare layout for this block.
	 * Adds the tabs and their content blocks for the 'Manage Affiliates' site in the
	 * admin area of magento.
	 * 
	 * @return parent::_prepareLayout()
	 */
	protected function _prepareLayout()
	{
		$helper = Mage::helper('pricecomparison/adminhtml_data');
		
		$this->addTab('general', array(
			'label' => $helper->__('General'),
			'title' => $helper->__('General'),
			'content'	=> $this->getLayout()->createBlock('pricecomparison/adminhtml_catalog_affiliates_tab_general')->toHtml(),
		));
		
		$this->addTab('add', array(
			'label' 	=> $helper->__('Add Affiliates'),
			'title' 	=> $helper->__('Add Affiliates'),
			'content'	=> $this->getLayout()->createBlock('pricecomparison/adminhtml_catalog_affiliates_tab_add')->toHtml(),
		));
		
		$this->addTab('import', array(
			'label' 	=> $helper->__('Import'),
			'title' 	=> $helper->__('Import'),
			'content'	=> $this->getLayout()->createBlock('pricecomparison/adminhtml_catalog_affiliates_tab_import')->toHtml(),
		));
		
		$this->addTab('export', array(
			'label' 	=> $helper->__('Export'),
			'title' 	=> $helper->__('Export'),
			'content'	=> $this->getLayout()->createBlock('pricecomparison/adminhtml_catalog_affiliates_tab_export')->toHtml(),
		));
		
		return parent::_prepareLayout();
	}
}
