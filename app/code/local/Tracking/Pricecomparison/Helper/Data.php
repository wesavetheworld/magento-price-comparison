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
 * Frontend helper class
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Returns the key for the cookie as defined in the config
	 * 
	 * @return string
	 */
	public function getCookieKey()
	{
		return Mage::getStoreConfig('tracking_pricecomparison_config/cookie/key');
	}
	
	public function getCookieUrlKey()
	{
		return Mage::getStoreConfig('tracking_pricecomparison_conig/cookie/urlKey');
	}
	
	/**
	 * Checks if the site was called with a specific parameter and
	 * sets a cookie if so.
	 * 
	 * @param string $pcsId    The id of the price-comparison-site (pcs).
	 * @param string $productId    The products id
	 * @param string $cookieKey    The key/name for the cookie
	 */
	public function captureReferral($pcsId, $productId, $cookieKey)
	{
		if ($pcsId) {
			Mage::getModel('core/cookie')->set(
				$cookieKey,
				$pcsId . '|' . $productId,
				$this->_getCookieLifetime()
			);
		}
	}
	
	/**
	 * Returns the predefined cookie lifetime
	 * 
	 * @return int/null
	 */
	protected function _getCookieLifetime()
	{
		$days = Mage::getStoreConfig('tracking_pricecomparsion_config/cookie/timeout');
		
		if ($days) {
			// convert to seconds
			return (int)86400 * $days;
		}
		else {
			return null;
		}
	}
}
