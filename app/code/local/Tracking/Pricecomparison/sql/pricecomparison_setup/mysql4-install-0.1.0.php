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
 * Setup script.
 * Adds the attribute set 'Pricecomparison Set'
 * for the entity type 'catalog/product'
 * and the group 'Pricecomparison Affiliates' to
 * this group.
 */

/**
 * @var $installer Mage_Eav_Model_Entity_Setup
 */
$installer = $this;

$installer->startSetup();


Mage::app()->getStore(0)->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

$websiteIds = Mage::getModel('core/website')->getCollection()
	->addFieldToFilter('website_id', array('neq' => 0))
	->getAllIds();


$attributeSetName = 'Pricecomparison Set';
$attributeGroupName = 'Pricecomparison Affiliates';

try {
	$entityTypeId = $installer->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);
}
catch (Exception $e) {
	Mage::log($e->getMessage() . '! Setup has failed.');
	$installer->endSetup();
	return;
}

try {
	$installer->addAttributeSet($entityTypeId, $attributeSetName);
		
	Mage::log('Attributeset ' . $attributeSetName . ' has been installed.');
}
catch (Exception $e) {
	Mage::log($e->getMessage());
}

$attributeSetId = false;
try {
	$attributeSetId = $installer->getAttributeSetId($entityTypeId, $attributeSetName, 'attribute_set_id');
	$installer->addAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupName);
	
	Mage::log('Attributegroup ' . $attributeGroupName . ' has been installed.');
}
catch (Exception $e) {
	Mage::log($e->getMessage());
}

$installer->endSetup();