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
 * Import form.
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Tab_Import_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepares this block's varien data form.
	 * 
	 * @return parent::_prepareForm()
	 */
	protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
			'id' => 'import_form',
			'action' => $this->getUrl('*/*/import', array('active_tab' => 'import')),
			'method' => 'post',
			'enctype' => 'multipart/form-data',
		));
		$form->setHtmlIdPrefix('import_');
		$form->setFieldNameSuffix('import');
		$form->setUseContainer(true);
		$this->setForm($form);
		$this->_generateFieldset();
		
		if (Mage::registry('tracking_pricecomparison_import')) {
			$form->setValues(Mage::registry('tracking_pricecomparison_import'));
		}
		
        return parent::_prepareForm();
    }
	
	/**
	 * Generates the fieldset for this form.
	 * 
	 * @return Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Tab_Import_Form
	 */
	protected function _generateFieldset()
	{
		$helper = Mage::helper('pricecomparison/adminhtml_data');
		
		$fieldset = $this->getForm()->addFieldset('importList', array(
			'legend' => $helper->__('Import List'),
			'class' => 'fieldset',
		));
		
		$fieldset->addField('select', 'select', array(
			'label' => $helper->__('Select import type'),
			'name' => 'select',
			'required' => true,
			'value' => '1',
			'values' => array(
				'1' => 'Pricelist',
				'2' => 'Affiliateslist',),
			'after_element_html' => $helper->generateAfterElementHtml('Please choose if you want to import a pricelist or a list of affiliates.'),
		));
		
		$fieldset->addField('file', 'file', array(
			'label' => $helper->__('Select file to import'),
			'title' => $helper->__('Search'),
			'name' => 'file',
			'value' => '',
			'required' => true,
			'after_element_html' => $helper->generateAfterElementHtml('Only .csv files are supported at the moment. To get the right format for the import, please export the empty list first.'),
		));
		
		return $this;
	}
}
