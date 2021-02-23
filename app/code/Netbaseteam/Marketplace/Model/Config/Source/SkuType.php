<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\Config\Source;

/**
 * Used in creating product for getting sku type value.
 */
class SkuType
{
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => 'static', 'label' => __('Static')],
            ['value' => 'dynamic', 'label' => __('Dynamic')],
        ];

        return $data;
    }
}
