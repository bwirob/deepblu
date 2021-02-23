<?php

namespace Netbaseteam\Marketplace\Model\Config\Source\Vacation;

class Disabletype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'product_disable', 'label' => __('Product Disable')],
            ['value' => 'add_to_cart_disable', 'label' => __('Add To Cart Disabled')]
        ];
    }
}
