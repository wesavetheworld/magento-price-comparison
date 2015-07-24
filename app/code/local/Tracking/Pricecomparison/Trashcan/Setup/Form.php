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
 
class Brille24_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Form_Setup_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id' => 'setup_form',
			'action' => $this->getUrl('*/*/saveSetup'),
			'method' => 'post',
		));
		
		$form->setUseContainer(true);
		$this->setForm($form);
		
		$helper = Mage::helper('pricecomparison');
		$fieldset = $form->addFieldset('display', array(
			'legend' => $helper->__('Display Settings'),
			'class' => 'fieldset-wide',
		));
		
		$fieldset->addField('testlabel', 'multiselect', array(
			'name' => 'testlabel',
			'label' => $helper->__('Mein Testlabel'),
		));
		
		if (Mage::registry('brille24_pricecomparison')) {
			$form->setValues(Mage::registry('brille24_pricecomparison'));
		}
		
		return parent::_prepareForm();
	}
}
	