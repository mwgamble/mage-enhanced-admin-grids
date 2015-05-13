<?php

class BL_CustomGrid_Block_Widget_Grid_Column_Renderer_Text_Urldecoded extends BL_CustomGrid_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $value = parent::_getValue($row);
        return urldecode($value);
    }

    /**
     * Render column for export
     *
     * @param Varien_Object $row
     * @return string
     */
    public function renderExport(Varien_Object $row)
    {
        return parent::render($row);
    }
}
