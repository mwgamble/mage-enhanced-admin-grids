<?php

class BL_CustomGrid_Model_Custom_Column_Urldecoded extends BL_CustomGrid_Model_Custom_Column_Simple_Duplicate
{
    /**
     * Return forced grid column block values
     * (you can check BL_CustomGrid_Model_Custom_Column_Abstract::getBlockValues() for the priorities
     *  of the different methods related to block values)
     *
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock Grid block
     * @param BL_CustomGrid_Model_Grid $gridModel Grid model
     * @param string $columnBlockId Grid column block ID
     * @param string $columnIndex Grid column index
     * @param array $params Customization params values
     * @param Mage_Core_Model_Store $store Column store
     * @return array
     */
    public function getForcedBlockValues(
        Mage_Adminhtml_Block_Widget_Grid $gridBlock,
        BL_CustomGrid_Model_Grid $gridModel,
        $columnBlockId,
        $columnIndex,
        array $params,
        Mage_Core_Model_Store $store
    ) {
        return array(
            'renderer' => 'customgrid/widget_grid_column_renderer_text_urldecoded',
            'filter_condition_callback' => array($this, 'addFilterToGridCollection'),
        );
    }

    /**
     * @param Mage_Core_Model_Resource_Url_Rewrite_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $columnBlock
     */
    public function addFilterToGridCollection($collection, Mage_Adminhtml_Block_Widget_Grid_Column $columnBlock)
    {
        $filter = $columnBlock->getFilter();
        $value = $filter->getValue();
        $filter->setValue(urlencode($value));
        $collection->addFieldToFilter($this->getDuplicatedFieldName(), $filter->getCondition());
        $filter->setValue($value);
    }
}
