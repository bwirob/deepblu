<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\Config\Source\Commission;

/**
 * Used in creating product for getting option value.
 */
class Option
{
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => '0', 'label' => __('Fixed')],
            ['value' => '1', 'label' => __('Percentage')],
        ];

        return $data;
    }
}
