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
 * @copyright  Copyright (c) 2015 Benoît Leulliette <benoit.leulliette@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class BL_CustomGrid_Block_Widget_Grid_Column_Filter_Text_Urldecoded extends BL_CustomGrid_Block_Widget_Grid_Column_Filter_Text
{
    /**
     * Overridden to strip domain from filter value, as it does not make sense
     *
     * @return string
     */
    public function getValue()
    {
        $parsedUrl = parse_url(parent::getValue());
        return (isset($parsedUrl["path"]) ? $parsedUrl["path"] : "") .
            (isset($parsedUrl["query"]) ? "?" . $parsedUrl["query"] : "") .
            (isset($parsedUrl["fragment"]) ? "#" . $parsedUrl["fragment"] : "");
    }

    /**
     * @return string
     */
    public function getUrlencodedValue()
    {
        $parsedUrl = parse_url($this->getValue());
        $urlPath = "";
        if (isset($parsedUrl["path"])) {
            $urlPath .= implode("/", array_map("rawurlencode", explode("/", $parsedUrl["path"])));
        }
        if (isset($parsedUrl["query"])) {
            // This urlencodes all the query parameters properly for us
            parse_str($parsedUrl["query"], $parsedQuery);
            $urlPath .= "?" . http_build_query($parsedQuery);
        }
        if (isset($parsedUrl["fragment"])) {
            $urlPath .= "#" . urlencode($parsedUrl["fragment"]);
        }
        return $urlPath;
    }

    /**
     * Return the collection condition(s) usable to filter on the given value with the LIKE function. Overridden to
     * URL-encode the filter value.
     *
     * @param string $value Filter value
     * @param string $filterMode Filter mode
     * @param bool $isNegative Whether negative filter is enabled
     * @return array
     */
    public function getLikeCondition($value, $filterMode, $isNegative)
    {
        return parent::getLikeCondition($this->getUrlencodedValue(), $filterMode, $isNegative);
    }
}
