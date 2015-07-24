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
 * Adminhtml helper
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Helper_Adminhtml_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Returns if the modul config flag is set.
	 * 
	 * @return boolean
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfigFlag('tracking_pricecomparison_config/general/status');
	}
	
	/**
	 * Returns the attribute set name defined in the config.
	 * 
	 * @return string
	 */
	public function getConfigAttributeSetName()
	{
		return Mage::getStoreConfig('tracking_pricecomparison_config/general/attributeSetName');
	}
	
	/**
	 * Returns the attribute group name defined in the config.
	 * 
	 * @return string
	 */
	public function getConfigAttributeGroupName()
	{
		return Mage::getStoreConfig('tracking_pricecomparison_config/general/attributeGroupName');
	}
	
	/**
	 * Wrapes a given string in html tags to be displayed as
	 * after element html (comment).
	 * 
	 * @var string $string    The string to be wraped.
	 * @return string
	 */
	public function generateAfterElementHtml($string)
	{
		return '<p class="nm"><small>' . $string . '</small></p>';
	}
	
	/**
	 * Recieves a form name as string and generates a javascript section as
	 * a string. This section is used to trigger the standard magento 
	 * operations on forms.
	 * 
	 * @var string $form    The name of a form.
	 * @return string $varienFormScript    The javascript section as string.
	 */
	public function getVarienFormScript($form)
	{
		$varienFormScript = '<script type="text/javscript">';
		$varienFormScript .= $form.'Form = new varienForm(\''.$form.'_form\',\'\');';
		$varienFormScript .= '</script>';
		
		return $varienFormScript;
	}
	
	/**
	 * Returns the option array of the attribute set collection
	 * filtered by entity type id and the config attribute set name.
	 * 
	 * @return array
	 */
	public function getAttributeSets()
	{
		$collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->addFieldToFilter('entity_type_id', array('eq' => Mage::getModel('catalog/product')->getResource()->getEntityType()->getId()))
			->addFieldToFilter('attribute_set_name', array('neq' => Mage::helper('pricecomparison/adminhtml_data')->getConfigAttributeSetName()))
			->load();
			
		return $collection->toOptionArray();
	}
}
