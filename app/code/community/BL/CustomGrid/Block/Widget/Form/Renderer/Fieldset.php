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

class BL_CustomGrid_Block_Widget_Form_Renderer_Fieldset extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bl/customgrid/widget/form/renderer/fieldset.phtml');
    }
    
    protected function _getConfigFormHelper()
    {
        return $this->helper('customgrid/config_form');
    }
    
    public function getDefaultCollapseState()
    {
        return (bool) $this->getDataSetDefault('default_collapse_state', true);
    }
    
    public function getFieldsetStateSaveUrl()
    {
        return $this->_getConfigFormHelper()->getFieldsetStateSaveUrl();
    }
    
    public function getElementCollapseState(Varien_Data_Form_Element_Abstract $element)
    {
        $state = $this->_getConfigFormHelper()->getFieldsetState($element->getHtmlId());
        return (!is_null($state) ? (bool) $state : $this->getDefaultCollapseState());
    }
}
