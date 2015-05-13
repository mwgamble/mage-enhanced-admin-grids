<?php

class BL_CustomGrid_Model_Grid_Type_Urlrewrite extends BL_CustomGrid_Model_Grid_Type_Abstract
{
    /**
     * @return string[]|string
     */
    protected function _getSupportedBlockTypes()
    {
        return array('adminhtml/urlrewrite_grid');
    }
}
