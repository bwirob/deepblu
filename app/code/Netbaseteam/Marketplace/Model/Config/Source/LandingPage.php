<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\Config\Source;

class LandingPage
{
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => '1', 'label' => __('Layout 1')],
            ['value' => '2', 'label' => __('Layout 2')],
            ['value' => '3', 'label' => __('Layout 3')],
        ];

        return $data;
    }
}
