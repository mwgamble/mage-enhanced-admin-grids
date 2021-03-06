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

class BL_CustomGrid_Model_Column_Renderer_Attribute_Price extends BL_CustomGrid_Model_Column_Renderer_Attribute_Abstract
{
    public function isAppliableToAttribute(
        Mage_Eav_Model_Entity_Attribute $attribute,
        BL_CustomGrid_Model_Grid $gridModel
    ) {
        return ($attribute->getFrontendInput() == 'price');
    }
    
    public function getColumnBlockValues(
        Mage_Eav_Model_Entity_Attribute $attribute,
        Mage_Core_Model_Store $store,
        BL_CustomGrid_Model_Grid $gridModel
    ) {
        return $this->_getRendererHelper()->getPriceValues($this, $store, $gridModel);
    }
}
