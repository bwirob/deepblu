<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\Config\Source\Locator;

class DistanceUnit
{
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => 'km', 'label' => __('Kilometers')],
            ['value' => 'mile', 'label' => __('Miles')],
        ];

        return $data;
    }
}
