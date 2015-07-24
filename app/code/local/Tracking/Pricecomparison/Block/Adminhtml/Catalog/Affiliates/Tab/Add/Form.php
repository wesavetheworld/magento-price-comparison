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
 * Add form
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Tab_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepares this block's varien data form.
	 * 
	 * @return parent::_prepareForm()
	 */
	protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
			'id' => 'add_form',
			'action' => $this->getUrl('*/*/add', array('active_tab' => 'add')),
			'method' => 'post',
			'enctype' => 'multipart/form-data',
		));
		$form->setHtmlIdPrefix('add_');
		$form->setFieldNameSuffix('add');
		$form->setUseContainer(true);
		$this->setForm($form);
		$this->_generateFieldset($form);
		
		if (Mage::registry('tracking_pricecomparison_add')) {
			$form->setValues(Mage::registry('tracking_pricecomparison_add'));
		}
		
        return parent::_prepareForm();
    }

	/**
	 * Generates the fieldset for this form.
	 * 
	 * @return Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Tab_Add_Form
	 */
	protected function _generateFieldset()
	{
		$helper = Mage::helper('pricecomparison/adminhtml_data');
		
		$fieldset = $this->getForm()->addFieldset('addAffiliate', array(
			'legend' => $helper->__('Add Affiliate'),
			'class' => 'fieldset',
		));
		
		$fieldset->addField('frontend_label', 'text', array(
			'name' => 'frontend_label',
			'required' => true,
			'class' => 'text-input required-entry',
			'label' => $helper->__('Name'),
			'title' => $helper->__('Name'),
		));
		
		$fieldset->addField('attribute_code', 'text', array(
			'name' => 'attribute_code',
			'required' => true,
			'class' => 'text-input required-entry',
			'label' => $helper->__('Unique Id'),
			'title' => $helper->__('Unique Id'),
			'after_element_html' => $helper->generateAfterElementHtml('For internal use. Must be unique with no spaces. Maximum length of attribute code must be less then 30 symbols.'),
		));
		
		$fieldset->addField('attribute_sets', 'multiselect', array(
			'name' => 'attribute_sets',
			'label' => $helper->__('Attributesets'),
			'title' => $helper->__('Attributesets'),
			'values' => $helper->getAttributeSets(),
			'after_element_html' => $helper->generateAfterElementHtml('Choose the attributeset(s) that should use the affiliate as attribute. For further information see the documentation regarding "Using attributesets".'),
			//@TODO Update documentation regarding this point
		));
		
		$fieldset->addField('attribute_sets_all', 'checkbox', array(
			'name' => 'attribute_sets_all',
			'label' => $helper->__('Select all attributesets'),
			'title' => $helper->__('Select all attributesets'),
			'checked' => false,
			'value' => '1',
			'after_element_html' => $helper->generateAfterElementHtml('Check this box if you want to apply the affiliate to all attributesets.'),
		));
		
		return $this;
	}
}
