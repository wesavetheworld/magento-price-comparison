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
 * Observer class.
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Model_Observer
{
	/**
	 * Sets the discount for the product in the product view if
	 * the customer was redirected from a price-comparison-site (pcs).
	 * 
	 * @param Varien_Event_Observer $observer    catalog_product_load_after
	 * @return void
	 */
	public function setPricecomparisonProductView(Varien_Event_Observer $observer)
	{
		$helper = Mage::helper('pricecomparison');
		$product = $observer->getEvent()->getProduct();
		$productId = $product->getId();
		$cookieKey = $helper->getCookieKey();
		
		$pcsCookie = Mage::getModel('core/cookie')->get($cookieKey);
		$pcsId = Mage::app()->getRequest()->getParam('pcs', false);
		
		if ($pcsId) {
			$helper->captureReferral($pcsId, $productId, $cookieKey);
			
			if ($pcsPrice = $product->getData($pcsId)) {
				$product->setFinalPrice($pcsPrice);
			}
		}
		else if ($pcsCookie) {
			$cookieData = explode('|', $pcsCookie);
			$pcsId = $cookieData[0];
			$pcsProductId = $cookieData[1];
			
			if ($pcsProductId !== $productId) {
				return;
			}
			
			if ($pcsPrice = $product->getData($pcsId)) {
				$product->setFinalPrice($pcsPrice);
			}
		}
	}
	
	/**
	 * Sets the discount for the products in the category view if
	 * the customer was redirected from a price-comparison-site (pcs).
	 * 
	 * @param Varien_Event_Observer $observer    catalog_product_collection_load_after
	 * @return void
	 */
	public function setPricecomparisonCategoryView(Varien_Event_Observer $observer)
	{
		$cookieKey = Mage::helper('pricecomparison')->getCookieKey();
		$pcsCookie = Mage::getModel('core/cookie')->get($cookieKey);
		// $pcsId = Mage::app()->getRequest()->getParam('pcs', false);
		
		// if ($pcsCookie && !$pcsId) {
		if ($pcsCookie) {
			$productCollection = $observer->getEvent()->getCollection();
			$cookieData = explode('|', $pcsCookie);
			$pcsId = $cookieData[0];
			$pcsProductId = $cookieData[1];
			
			foreach ($productCollection as $product) {
				$productId = $product->getId();
				
				if ($productId === $pcsProductId) {
					$loadedProduct = Mage::getModel('catalog/product')->load($productId);
					
					if ($pcsPrice = $loadedProduct->getData($pcsId)) {
						$product->setFinalPrice($pcsPrice);
					}
				}
			}
		}
	}
	
	/**
	 * Sets the discount for the products in the cart if
	 * the customer was redirected from a price-comparison-site (pcs)
	 * and a cookie indicating that exists.
	 * 
	 * @param Varien_Event_Observer $observer    checkout_cart_product_add_after
	 * @return void
	 */
	public function setPricecomparisonCartView(Varien_Event_Observer $observer)
	{
		$cookieKey = Mage::helper('pricecomparison')->getCookieKey();
		$item = $observer->getQuoteItem();
		$item = ($item->getParentItem() ? $item->getParentItem() : $item);
		$product = $item->getProduct();
		$productId = $product->getId();
		$pcsCookie = Mage::getModel('core/cookie')->get($cookieKey);
		
		if ($pcsCookie) {
			$cookieData = explode('|', $pcsCookie);
			$pcsId = $cookieData[0];
			$pcsProductId = $cookieData[1];
			
			if ($productId !== $pcsProductId) {
				return;
			}
			
			if ($pcsPrice = $product->getData($pcsId)) {
				$item->setCustomPrice((string)$pcsPrice);
				$item->setOriginalCustomPrice((string)$pcsPrice);
				$item->getProduct()->setIsSuperMode(true);
			}
		}
	}
}
