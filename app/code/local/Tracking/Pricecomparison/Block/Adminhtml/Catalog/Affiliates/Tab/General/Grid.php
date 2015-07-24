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
 * General grid.
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Tab_General_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('generalForm');
		$this->setDefaultSort('id');
		$this->setDefaultDir('asc');
	}
	
	/**
     * Prepare grid collection object.
     *
     * @return parent::_prepareCollection()
     */
	protected function _prepareCollection()
	{
		/** @var Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection */
		$pricecomparisonSet = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->addFieldToFilter('attribute_set_name', array('eq' => 'Pricecomparison Set'))
			->load();
		
		$setId = $pricecomparisonSet->getFirstItem()->getAttributeSetId();
		
		/** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection */
		$attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
			->setAttributesetFilter($setId);
		
		foreach ($attributeCollection as $item) {
			if ($item->isScopeGlobal()) {
				$item->setScope('Global');
			}
			elseif ($item->isScopeWebsite()) {
				$item->setScope('Website');
			}
			elseif ($item->isScopeStore()) {
				$item->setScope('Store');
			}
		}
		
		$this->setCollection($attributeCollection);
		
		return parent::_prepareCollection();
	}
	
	/**
	 * Prepare the columns to be displayed.
	 * 
	 * @return parent::_prepareColumns()
	 */
	protected function _prepareColumns()
	{
		$helper = Mage::helper('pricecomparison/adminhtml_data');
		
		$this->addColumn('attribute_code', array(
			'header' => $helper->__('Attribute Code'),
			'index' => 'attribute_code',
		));
		
		$this->addColumn('attribute_label', array(
			'header' => $helper->__('Attribute Label'),
			'index' => 'frontend_label',
		));
		
		$this->addColumn('attribute_scope', array(
			'header' => $helper->__('Scope'),
			'index' => 'scope',
		));
		
		return parent::_prepareColumns();
	}
	
	/**
     * Return row url for js event handlers
     *
     * @param Mage_Catalog_Model_Product|Varien_Object
     * @return string
     */
    public function getRowUrl($item)
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product_attribute/edit/', array('attribute_id' => $item->getAttributeId()));
    }
}