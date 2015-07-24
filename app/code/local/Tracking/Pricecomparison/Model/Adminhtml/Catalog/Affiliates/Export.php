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
	 * This entity, either tpc_price_list
	 * or tpc_affiliates_list.
	 * tpc = TrackingPricecomparison.
	 * 
	 * @var string
	 */
	protected $_entity;
	
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
			$this->_entity = 'tpc_price_list';
		}
		elseif ($select === self::AFFILIATE_LIST) {
			$this->_createAffiliatesList();
			$this->_entity = 'tpc_affiliates_list';
		}
		else {
			Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('Invalid selection of import list.'));
		}
		
		return $this;
	}
	
	/**
	 * Creates the content for the csv file as a price list of the affiliates' prices.
	 * 
	 * @throws Mage_Core_Exception
	 * @return Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Export
	 */
	protected function _createPriceList()
	{
		$helper = Mage::helper('pricecomparison/adminhtml_data');
		$storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
		$rowData = array();
		$attributesData = array();
		$countAttributes = 0;
		$this->setHeaderCols($this->_getPriceHeaderCols());
		
		// Get 'Pricecomparison Set' set to get its id.
		/** @var Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection */
		$attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->addFieldToFilter('attribute_set_name', $helper->getConfigAttributeSetName())
			->addFieldToSelect(array('attribute_set_name', 'attribute_set_id'))
			->load();
		$attributeSetId = $attributeSetCollection->getFirstItem()->getAttributeSetId();
		
		// Get all affiliates attributes.
		/** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection */
		$attributes = Mage::getResourceModel('catalog/product_attribute_collection')
			->setAttributeSetFilter($attributeSetId)
			->load();
			
		// Only if there are affiliates handle them.
		if ($attributes->count() > 0) {
			// Simplify access to needed attribute data.
			foreach ($attributes as $attribute) {
				$attributesData['codes'][] = $attribute->getAttributeCode();
				$attributesData['names'][] = $attribute->getFrontendLabel();
				$countAttributes++;
			}
			$attributes = null;
			
			// Get all products with their names which have the affiliates as attribute.
			/** @var Mage_Catalog_Model_Resource_Product_Collection */
			$productCollection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('name')
				->addAttributeToSelect($attributesData['codes'])
				->load();
				
			/** @var Mage_Eav_Model_Config */
			$eavConfig = Mage::getModel('eav/config');
			
			foreach ($productCollection as $product) {
				$counter = 0;
				// Get all attributecodes for the current product.
				$attributeCodes = $eavConfig->getEntityAttributeCodes(Mage_Catalog_Model_Product::ENTITY, $product);
				// Check product for all available affiliates and write row for everyone found.
				while ($counter < $countAttributes) {
					$attributeCode = $attributesData['codes'][$counter];
					$attributeName = $attributesData['names'][$counter];
					
					if (in_array($attributeCode, $attributeCodes)) {
						$rowData['product_id'] = $product->getEntityId();
						$rowData['sku[optional]'] = $product->getSku();
						$rowData['product_name[optional]'] = $product->getName();
						$rowData['affiliate_code'] = $attributeCode;
						$rowData['affiliate_name[optional]'] = $attributeName;
						$rowData['price'] = $product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product);//$product->getResource()->getAttributeRawValue($product->getEntityId(), $attributeCode, $storeId);
						$this->writeRow($rowData);
					}
					$counter++;
				}
			}
		}
		return $this;
	}
	
	/**
	 * Creates the content for the csv file as a list of affiliates.
	 * 
	 * @throws Mage_Core_Exception
	 * @return Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Export
	 */
	protected function _createAffiliatesList()
	{
		$helper = Mage::helper('pricecomparison/adminhtml_data');
		$rowData = array();
		$attributeSetIds = array();
		$this->setHeaderCols($this->_getAffiliatesHeaderCols());
		
		/** @var Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection */
		$attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->addFieldToFilter('entity_type_id', Mage::getModel('catalog/product')->getResource()->getEntityType()->getId())
			->addFieldToSelect(array('attribute_set_name', 'attribute_set_id'))
			->load();
		
		// Create an array of key=attribute_set_id => value=attribute_set_name and check for the 'Pricecomparison Set'.
		foreach ($attributeSetCollection->getItems() as $setId => $set) {
			$attributeSetIds[$setId] = $set->getAttributeSetName();
			if ($attributeSetIds[$setId] === $helper->getConfigAttributeSetName()) {
				// Since every affiliate gets saved in the 'Pricecomparison Set', just load the attributes of this set.
				/** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection */
				$attributes = Mage::getResourceModel('catalog/product_attribute_collection')
					->setAttributeSetFilter($setId)
					->addSetInfo(true)
					->load();
			}
		}
		
		if ($attributes) {
			foreach ($attributes->getItems() as	$attribute) {
				// Create a row for every attribute set.
				foreach ($attribute->getAttributeSetInfo() as $setId => $value) {
					$rowData['affiliate_code'] = $attribute->getAttributeCode();
					$rowData['affiliate_name'] = $attribute->getFrontendLabel();
					$rowData['attribute_set_id[optional]'] = $setId;
					$rowData['attribute_set_name[optional]'] = $attributeSetIds[$setId];
					$this->writeRow($rowData);
				}
			}
		}
		else {
			Mage::throwException($helper->__('Affiliates could not be loaded from attribute set ' . $helper->getConfigAttributeSetName()));
		}
		
		return $this;
	}
	
	/**
	 * Returns the header column titles for the price list.
	 * 
	 * @return array
	 */
	protected function _getPriceHeaderCols()
	{
		return array(
			'product_id',
			'sku[optional]',
			'product_name[optional]',
			'affiliate_code',
			'affiliate_name[optional]',
			'price',
		);
	}

	/**
	 * Returns the header column titles for the list of affiliates.
	 * 
	 * @return array
	 */
	protected function _getAffiliatesHeaderCols()
	{
		return array(
			'affiliate_code',
			'affiliate_name',
			'attribute_set_id[optional]',
			'attribute_set_name[optional]',
		);
	}
	
	/**
     * Override standard entity getter.
     *
     * @throw Exception
     * @return string
     */
    public function getEntity()
    {
        if (is_null($this->_entity)) {
            Mage::throwException(Mage::helper('importexport')->__('Entity is unknown'));
        }
        return $this->_entity;
    }
	
	/**
     * Return file name for downloading.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->getEntity() . '_' . date('Ymd_His') .  '.' . $this->getFileExtension();
    }
}
