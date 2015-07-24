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
 * Export model to manage the export of the affiliates
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Export extends Mage_ImportExport_Model_Export_Adapter_Csv
{
	/**
	 * Form field select constants
	 */
	const PRICE_LIST = '1';
	const AFFILIATE_LIST = '2';
	
	/**
	 * Starts the export of the data to a csv file.
	 * 
	 * @var string $select    The string value of the selected list.
	 * @throws Mage_Core_Exception
	 */
	public function startExport($select)
	{
		if (!is_string($select)) {
			Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('Selection value must be a string.'));
		}
		
		if ($select === self::PRICE_LIST) {
			$this->_createPriceList();
		}
		elseif ($select === self::AFFILIATE_LIST) {
			$this->_createAffiliateList();
		}
		else {
			Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('Invalid selection of import list.'));
		}
		
		
	}
	
	protected function _createPriceList()
	{
		
	}
	
	protected function _createAffiliateList()
	{
		$helper = Mage::helper('pricecomparison/adminhtml_data');
		$this->setHeaderCols($this->_getAffiliateHeaderCols());
		$rowData = array();
		
		$attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->addFieldToFilter('attribute_set_name', $helper->getConfigAttributeSetName())
			->load();
			
		$attributeSetId = $attributeSetCollection->getFirstItem()
			->getAttributeSetId();
			
		
		
		$attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->addFieldToFilter('entity_type_id', Mage::getModel('catalog/product')->getResource()->getEntityType()->getId())
			->addFieldToSelect(array('attribute_set_name', 'attribute_set_id'))
			->load();
			
		foreach ($attributeSetCollection->getItems() as $setId => $set) {
			$attributeSetIds[$key] = $set->getAttributeSetName();
			
			if ($attributeSetIds[$key] === $helper->getConfigAttributeSetName()) {
				$attributes = Mage::getResourceModel('catalog/product_attribute_collection')
					->setAttributeSetFilter($attributeSetIds[$key])
					->addSetInfo(true)
					->load();
			}
		}
		
		foreach ($attributes->getItems() as	$attribute) {
			foreach ($attribute->getAttributeSetInfo as $key => $value) {
				$rowData['affiliate_code'] = $attribute->getAttributeCode();
				$rowData['affiliate_name'] = $attribute->getFrontendLabel();
				$rowData['attribute_set_id'] = $key;
				$rowData['attribute_set_name'] = '';
			}
			
		}
		die();
	}
	
	protected function _getPriceHeaderCols()
	{
		return $headerCols = array(
			'product_id',
			'sku[optional]',
			'product_name[optional]',
			'affiliate_code',
			'affiliate_name[optional]',
			'price',
		);
	}

	protected function _getAffiliateHeaderCols()
	{
		return $headerCols = array(
			'affiliate_code',
			'affiliate_name',
			'attribute_set_id',
			'attribute_set_name',
		);
	}
}
