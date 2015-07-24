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
 * Container for the content of all tabs.
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Container extends Mage_Adminhtml_Block_Widget_Container
{
	/**
	 * Constructor
	 */
    public function __construct()
	{
		parent::__construct();
		$this->setTemplate('tracking/pricecomparison/container.phtml');
		$this->setId('pricecomparison_container');
	}
	
	/**
	 * Prepares layout.
	 * 
	 * @return parent::_returnLayout()
	 */
	protected function _prepareLayout()
    {
    	return parent::_prepareLayout();
	}
}
