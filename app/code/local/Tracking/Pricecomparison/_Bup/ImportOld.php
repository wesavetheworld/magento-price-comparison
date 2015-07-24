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
 
class Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Tab_Import extends Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Tab_Container
{
	public function __construct()
    {
        parent::__construct();
		
		$this->_blockGroup = 'pricecomparison';
		$this->_controller = 'adminhtml_catalog_affiliates_tab';
		$this->_mode = 'import';
		$this->_headerText = Mage::helper('pricecomparison/adminhtml_data')->__('Import');
		
        $this->_addButton('save', array(
            'label'     => Mage::helper('pricecomparison/adminhtml_data')->__('Save'),
            'onclick'   => 'importForm.submit();',
            'class'     => 'save',
        ), 1);
		
		$this->_getCustomFormScript();
    }
	
	protected function _prepareLayout()
	{
		// var_dump($this->getNameInLayout());die();
		return parent::_prepareLayout();
	}
}