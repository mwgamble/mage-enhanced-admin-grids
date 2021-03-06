<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   BL
 * @package    BL_CustomGrid
 * @copyright  Copyright (c) 2014 Benoît Leulliette <benoit.leulliette@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class BL_CustomGrid_Block_Options_Source_Edit_Tab_Custom extends BL_CustomGrid_Block_Widget_Form implements
    Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return $this->__('Custom List');
    }
    
    public function getTabTitle()
    {
        return $this->__('Custom List');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }
    
    protected function _prepareForm()
    {
        $optionsSource = $this->getOptionsSource();
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('custom_list', array('legend' => $this->__('Custom List')));
        
        $fieldset->addField(
            'options',
            'text',
            array(
                'name'  => 'options',
                'label' => $this->__('Options List'),
                'class' => 'required-entry',
                'value' => $optionsSource->getData('options'),
            )
        );
        
        $optionsRenderer = $this->getLayout()->createBlock('customgrid/options_source_edit_tab_custom_list');
        $form->getElement('options')->setRenderer($optionsRenderer);
        $this->setForm($form);
        
        return $this;
    }
}