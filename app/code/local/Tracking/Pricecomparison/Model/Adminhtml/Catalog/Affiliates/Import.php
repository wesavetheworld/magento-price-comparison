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
 * Import model to manage the imports of the affiliates.
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Import extends Mage_ImportExport_Model_Import_Adapter_Csv
{
	/**
	 * Form field select constants
	 */
	const PRICE_LIST = '1';
	const AFFILIATE_LIST = '2';
	
	/**
	 * Column names for importing a price list.
	 * 
	 * @var array
	 */
	protected $_priceList;
	
	/**
	 * Column names for importing a list of affiliates.
	 * 
	 * @var array
	 */
	protected $_affiliateList;
	
	/**
	 * Container for the collected price data of the csv file
	 * 
	 * @var array
	 */
	protected $_priceData;
	
	/**
	 * Container for the collected affiliate data of the csv file
	 * 
	 * @var array
	 */
	protected $_affiliateData;
	
	/**
     * Method called as last step of object instance creation. Can be overrided in child classes.
     *
     * @return Mage_ImportExport_Model_Import_Adapter_Abstract
     */
    protected function _init()
    {
    	$this->_priceList = array(
    		'product_id',
			'sku[optional]',
			'product_name[optional]',
			'affiliate_code',
			'affiliate_name[optional]',
			'price',
		);
		
		$this->_affiliateList = array(
			'affiliate_code',
			'affiliate_name',
			'attribute_set_id[optional]',
			'attribute_set_name[optional]',
		);
		
		$this->_priceData = array();
		$this->_affiliateData = array();
		
        return parent::_init();
    }
	
	/**
	 * Starts the import of the csv file data.
	 * 
	 * @var string $select    A string indicating which type of list needs to be handled.
	 * @throws Mage_Core_Exception
	 * @return Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Export
	 */
	public function startImport($select)
	{
		if (!is_string($select)) {
			Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('Selection value must be a string.'));
		}
		
		if ($select === self::PRICE_LIST) {
			$this->_validatePriceListColumns();
			$this->_getPriceData();
			$this->_updatePrices();
		}
		elseif ($select === self::AFFILIATE_LIST) {
			$this->_validateAffiliateListColumns();
			$this->_getAffiliatesData();
			var_dump($this->_affiliateData);die();
			$this->_updateAffiliates();
		}
		else {
			Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('Invalid selection of import list.'));
		}
		
		return $this;
	}

	/**
	 * Updates all affiliate attributes with the prices given retrived by _getPriceData().
	 * $attributeData[0] = attribute_code
	 * $attributeData[1] = attribute_value
	 * 
	 * @return Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Export
	 */
	protected function _updatePrices()
	{
		$helper = Mage::helper('pricecomparison/adminhtml_data');
		$session = Mage::getSingleton('adminhtml/session');
		$storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
		$entity = Mage_Catalog_Model_Product::ENTITY;
		/** @var Mage_Catalog_Model_Resource_Eav_Attribute */
		$attrResource = Mage::getResourceSingleton('catalog/eav_attribute');
		/** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Action */
		$massActionResource = Mage::getResourceSingleton('catalog/eav_mysql4_product_action');
		
		// Preparing SQL-Query to check if the product has the affiliate as attribute.
		/** @var Mage_Core_Model Resource */
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$catalogProductEntityTable = $resource->getTableName('catalog_product_entity');
		$eavEntityAttributeTable = $resource->getTableName('eav_entity_attribute');
		$eavAttributeTable = $resource->getTableName('eav_attribute');
		$query = $readConnection->prepare(
			"SELECT ea.attribute_id
			 FROM {$catalogProductEntityTable} pe, {$eavEntityAttributeTable} ea, {$eavAttributeTable} a
			 WHERE ea.attribute_id = a.attribute_id
			 AND ea.attribute_id = a.attribute_id
			 AND ea.attribute_set_id = pe.attribute_set_id
			 AND pe.entity_id = :product_id
			 AND a.attribute_code = :attribute_code"
		);
		
		// Process every product of the import.
		foreach ($this->_priceData as $productId => $values) {
			$query->bindParam(':product_id', $productId);
			// Process every affiliate/attribute of the current product.
			foreach ($values as $attributeData) {				
				$attributeCode = $attributeData[0];
				$query->bindParam('attribute_code', $attributeCode);
				// Query to check if this affiliate/attribue exists for this product.
				$query->execute();
				$value = $query->fetch();
				
				if ($attr = $attrResource->getIdByCode($entity, $attributeCode)) {
					if ($value !== false) {
						$massActionResource->updateAttributes(array($productId), array($attributeCode => $attributeData[1]), $storeId);
					}
					else {
						$session->addNotice($helper->__('The product with the id ' . $productId . ' doesn\'t have the attribute with the code ' . $attributeCode));
					}
				}
				else {
					Mage::throwException($helper->__('Affiliate with the code ' . $attributeCode . ' doesn\'t exist.'));
				}
			}
			
		}
		return $this;
	}
	
	/**
	 * Updates all affiliate attributes with the data given retrived by _getAffiliateData()
	 * 
	 * @return Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Export
	 */
	protected function _updateAffiliates()
	{
		
		return $this;
	}
	
	/**
	 * Handles the given file to extract the price data.
	 * $_currentRow[0] = affiliate_code
	 * $_currentRow[1] = affiliate_name
	 * $_currentRow[2] = attribute_set_id
	 * 
	 * @return Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Export
	 */
	protected function _getAffiliatesData()
	{
		while ($this->valid()) {
			if (!isset($this->_currentRow[0])) {
				Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('CSV-Line \'' . ($this->_currentKey + 1) . '\': Missing affiliate_code!'));
			}
			if (!isset($this->_currentRow[1])) {
				Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('CSV-Line \'' . ($this->_currentKey + 1) . '\': Missing affiliate_name!'));
			}
			// if (($setId = (int)$this->_currentRow[2]) === 0) {
				// Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('CSV-Line \'' . ($this->_currentKey + 1) . '\': Invalid set id! \'' . $this->_currentRow[2] . '\''));
			// }
			$this->_affiliateData[] = array($this->_currentRow[0], $this->_currentRow[1], $this->_currentRow[2]);
			$this->next();
		}
		
		return $this;
	}
	
	/**
	 * Handles the given file to extract the price data.
	 * $_currentRow[0] = product_id
	 * $_currentRow[3] = affiliate_code
	 * $_currentRow[5] = price
	 * 
	 * @return Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates_Export
	 */
	protected function _getPriceData()
	{
		$productId = 0;
		while ($this->valid()) {
			if (($productId = (int)$this->_currentRow[0]) === 0) {
				Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('CSV-Line \'' . ($this->_currentKey + 1) . '\': Invalid product id \'' . $this->_currentRow[0] . '\''));
			}
			if (!isset($this->_currentRow[3])) {
				Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('CSV-Line \'' . ($this->_currentKey + 1) . '\': Missing attribute_code!'));
			}
			
			$this->_priceData[$productId][] = array($this->_currentRow[3], $this->_currentRow[5]);
			$this->next();
		}
		
		return $this;
	}
	
	/**
	 * Validates that the file has the right amount and names for the columns
	 * to be handled as price list.
	 * 
	 * @throws Mage_Core_Exception
	 * @return void
	 */
	protected function _validatePriceListColumns()
	{
		if (!empty(array_diff_assoc($this->_priceList, $this->getColNames()))) {
			Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('Invalid column-names or number of columns.'));
		}
	}
	
	/**
	 * Validates that the file has the right amount and names for the columns
	 * to be handled as list of affiliates.
	 * 
	 * @throws Mage_Core_Exception
	 * @return void
	 */
	protected function _validateAffiliateListColumns()
	{
		if (!empty(array_diff_assoc($this->_affiliateList, $this->getColNames()))) {
			Mage::throwException(Mage::helper('pricecomparison/adminhtml_data')->__('Invalid column-names or number of columns.'));
		}
	}
}
