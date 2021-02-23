<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\Config\Source\Commission;

/**
 * Used in creating product for getting type value.
 */
class Type
{
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => '0', 'label' => __('Per Item')],
            ['value' => '1', 'label' => __('Per Order')],
        ];

        return $data;
    }
}
