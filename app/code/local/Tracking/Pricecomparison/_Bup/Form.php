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
 
class Tracking_Pricecomparison_Model_Adminhtml_Catalog_Affiliates extends Mage_Core_Model_Abstract
{
	protected $_entityTypeId;
	
	protected function _construct()
	{
		$this->_entityTypeId = Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId();
	}
	
	protected function _getConfigAttributeSetName()
	{
		return Mage::getStoreConfig('tracking_pricecomparison_config/general/attributeSet');
	}
	
	protected function _getConfigAttributeGroupName()
	{
		Mage::getStoreConfig('tracking_pricecomparison_config/general/attributeGroup');
	}
	
	protected function _getAttributeSetId($setName)
	{
		$id = Mage::getModel('eav/entity_attribute_set')
			->getResourceCollection()
			->addFilter('attribute_set_name', array('eq' => $setName));
		return $id;
	}
	
	public function addAffiliate($data = null)
	{
		if (!isset($data['add'])) {
			return $this;
		}
		$attributeData = $this->_getAttributeData($data['add']);
		$this->_createAttribute($attributeData);
		
		$id = $this->_getAttributeSetId($this->_getConfigAttributeSetName());
		$data['add']['attribute_sets'][] = $id;
		$this->_addAttributeToSets($data['add']['attribute_sets']);
		
		return $this;
	}
	 
	/**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        if ($data) {
            /** @var $helperCatalog Mage_Catalog_Helper_Data */
            $helperCatalog = Mage::helper('catalog');
            //labels
            foreach ($data['frontend_label'] as & $value) {
                if ($value) {
                    $value = $helperCatalog->stripTags($value);
                }
            }

            if (!empty($data['option']) && !empty($data['option']['value']) && is_array($data['option']['value'])) {
                foreach ($data['option']['value'] as $key => $values) {
                    $data['option']['value'][$key] = array_map(array($helperCatalog, 'stripTags'), $values);
                }
            }
        }
        return $data;
    }
	
	protected function _createAttribute($data)
	{
		if (!empty($data)) {
            /** @var $session Mage_Admin_Model_Session */
            $session = Mage::getSingleton('adminhtml/session');

            /* @var $model Mage_Catalog_Model_Entity_Attribute */
            $model = Mage::getModel('catalog/resource_eav_attribute');
            /* @var $helper Tracking_Pricecomparison_Helper_Adminhtml_Form */
            $helper = Mage::helper('catalog/product');

			$data['frontend_input'] = 'price';
			
            //validate attribute_code
            if (isset($data['attribute_code'])) {
                $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{1,254}$/'));
                if (!$validatorAttrCode->isValid($data['attribute_code'])) {
                    $session->addError(
                        Mage::helper('catalog')->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.')
                    );
                    $this->_redirect('*/*/add', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }


            //validate frontend_input
            if (isset($data['frontend_input'])) {
                /** @var $validatorInputType Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
                $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
                if (!$validatorInputType->isValid($data['frontend_input'])) {
                    foreach ($validatorInputType->getMessages() as $message) {
                        $session->addError($message);
                    }
                    $this->_redirect('*/*/add', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            /**
            * @todo add to helper and specify all relations for properties
            */
            $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
            $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);

            if (!isset($data['is_configurable'])) {
                $data['is_configurable'] = 0;
            }
            if (!isset($data['is_filterable'])) {
                $data['is_filterable'] = 0;
            }
            if (!isset($data['is_filterable_in_search'])) {
                $data['is_filterable_in_search'] = 0;
            }

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            
            $data['default_value'] = '';

            if(!isset($data['apply_to'])) {
                $data['apply_to'] = array();
            }

            //filter
            $data = $this->_filterPostData($data);
            $model->addData($data);
			
            $model->setEntityTypeId($this->_entityTypeId);
            $model->setIsUserDefined(1);

            try {
                $model->save();
                $session->addSuccess(
                    Mage::helper('catalog')->__('The affiliate has been saved.'));

                /**
                 * Clear translation cache because attribute labels are stored in translation
                 */
                Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
                $session->setAttributeData(false);
                
                return;
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $session->setAttributeData($data);
                return;
            }
        }
	}
	
	protected function _getAttributeData($data)
	{
		$attributeData = array(
			'attribute_code' => $data['attribute_code'],
			'is_global' => '1',
			'frontend_input' => 'price',
			'default_value_text' => '',
	        'default_value_yesno' => '0',
	        'default_value_date' => '',
	        'default_value_textarea' => '',
			'is_unique' => '0',
			'is_required' => '0',
			'apply_to' => array(),
			'is_configurable' => '0',
			'is_searchable' => '0',
			'is_visible_in_advanced_search' => '0',
			'is_comparable' => '0',
			'is_used_for_price_rules' => '0',
			'is_wysiwyg_enabled' => '0',
			'is_html_allowed_on_front' => '1',
			'is_visible_on_front' => '0',
			'used_in_product_listing' => '0',
			'used_for_sort_by' => '0',
			'frontend_label' => array(
				0 => $data['frontend_label'],
				1 => '',
				2 => '',
				3 => '',
				4 => '',
			)
		);
		
		return $attributeData;
	}
	
	protected function _addAttributeToSets($attributeSetIds)
	{
		$attributeId = Mage::getResourceModel('eav/entity_attribute')
			->getIdByCode($this->_entityTypeId, $data['attribute_code']);

		$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
		$groupName = $this->_getConfigAttributeGroupName();
		
		foreach ($attributeSetIds as $setId) {
			$group = Mage::getModel('eav/entity_attribute_group');
			
			$group->setAttributeGroupName($groupName)
				->setAttributeSetId($setId)
				->setSortOrder(100);
			
			$group->save();
		}
	}
}
