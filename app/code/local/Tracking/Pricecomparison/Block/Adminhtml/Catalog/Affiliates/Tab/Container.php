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
 * General container used to define common parameters for all form containers.
 * 
 * @author Tjard Kügler
 */
class Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Tab_Container extends Mage_Adminhtml_Block_Widget_Container
{
	/**
	 * Id for this container object.
	 * 
	 * @var string
	 */
    protected $_objectId = 'id';
	
	/**
	 * Array for the form scripts.
	 * 
	 * @var array
	 */
    protected $_formScripts = array();
	
	/**
	 * Array for the form init scripts.
	 * 
	 * @var array
	 */
    protected $_formInitScripts = array();
	
	/**
	 * The mode for the _prepareLayout function.
	 * 
	 * @var string
	 */
    protected $_mode;
	
	/**
	 * The block group for the _prepareLayout function.
	 * 
	 * @var string
	 */
    protected $_blockGroup;
	
	/**
	 * The controller for the _prepareLayout function.
	 * 
	 * @var string
	 */
	protected $_controller;
	
	/**
	 * The element for the _prepareLayout function.
	 * 
	 * @var string
	 */
	protected $_element;
	
	/**
	 * Constructor
	 */
    public function __construct()
    {
        parent::__construct();

        if (!$this->hasData('template')) {
            $this->setTemplate('widget/form/container.phtml');
        }
		
        $this->_addButton('reset', array(
            'label'     => Mage::helper('adminhtml')->__('Reset'),
            'onclick'   => 'setLocation(window.location.href)',
        ), -1);
	}
	
	/**
	 * Adds a customized form script to this object's from scripts.
	 * 
	 * @return Tracking_Pricecomparison_Block_Adminhtml_Catalog_Affiliates_Tab_Container
	 */
	protected function _addCustomFormScript()
	{
		$this->_formScripts = array($this->_mode."Form = new varienForm('".$this->_mode."_form', '');");
		return $this;
	}

	/**
	 * Prepares the layout.
	 * 
	 * @return parent::_prepareLayout()
	 */
    protected function _prepareLayout()
    {
        if ($this->_blockGroup && $this->_controller && $this->_mode && $this->_element) {
            $this->setChild('form', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_' . $this->_element));
        }
        return parent::_prepareLayout();
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    /**
     * Get form save URL
     *
     * @deprecated
     * @see getFormActionUrl()
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getFormActionUrl();
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('*/' . $this->_controller . '/save');
    }

	/**
	 * Gets the form html from its child.
	 * 
	 * @return string
	 */
    public function getFormHtml()
    {
        $this->getChild('form')->setData('action', $this->getSaveUrl());
        return $this->getChildHtml('form');
    }
	
	/**
	 * Gets this object's form init scripts.
	 * 
	 * @return string
	 */
    public function getFormInitScripts()
    {
        if ( !empty($this->_formInitScripts) && is_array($this->_formInitScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formInitScripts) . '</script>';
        }
        return '';
    }
	
	/**
	 * Gets this object'S from scripts.
	 * 
	 * @return string
	 */
    public function getFormScripts()
    {
        if ( !empty($this->_formScripts) && is_array($this->_formScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formScripts) . '</script>';
        }
        return '';
    }
	
	/**
	 * Gets the header css class.
	 * 
	 * @return string
	 */
    public function getHeaderCssClass()
    {
        return 'icon-head head-' . strtr($this->_controller, '_', '-');
    }

	/**
	 * Gets the header html.
	 * 
	 * @return string
	 */
    public function getHeaderHtml()
    {
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }

    /**
     * Set data object and pass it to form
     *
     * @param Varien_Object $object
     * @return Mage_Adminhtml_Block_Widget_Form_Container
     */
    public function setDataObject($object)
    {
        $this->getChild('form')->setDataObject($object);
        return $this->setData('data_object', $object);
    }
}
