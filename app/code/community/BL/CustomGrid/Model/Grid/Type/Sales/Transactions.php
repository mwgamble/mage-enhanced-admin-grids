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

class BL_CustomGrid_Model_Grid_Type_Sales_Transactions extends BL_CustomGrid_Model_Grid_Type_Abstract
{
    /**
     * @return string[]|string
     */
    protected function _getSupportedBlockTypes()
    {
        return array('adminhtml/sales_transactions_grid');
    }

    /**
     * Do some actions before grid collection is prepared
     *
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock Grid block
     * @param bool $firstTime Whether this is the first (= incomplete) grid collection preparation
     * @return BL_CustomGrid_Model_Grid_Type_Sales_Transactions
     */
    public function beforeGridPrepareCollection(Mage_Adminhtml_Block_Widget_Grid $gridBlock, $firstTime = true)
    {
        if (!$firstTime) {
            $gridBlock->blcg_addCollectionCallback(
                'before_prepare',
                array($this, 'removeFirstTimeCollection'),
                array(),
                true
            );
        }
        return $this;
    }

    /**
     * When calling _prepareCollection(), this grid by default looks to see if one is already set, rather
     * than just overriding any existing collection. This is unique in Magento, and causes all sorts of
     * problems because the collection has already been loaded.
     *
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     * @param Varien_Data_Collection_Db $collection
     * @param bool $firstTime
     */
    public function removeFirstTimeCollection(
        Mage_Adminhtml_Block_Widget_Grid $gridBlock,
        Varien_Data_Collection_Db $collection,
        $firstTime
    ) {
        if (!$firstTime) {
            $gridBlock->blcg_unsetCollection();
        }
    }
}
