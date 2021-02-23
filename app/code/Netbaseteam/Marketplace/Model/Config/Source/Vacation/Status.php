<?php

namespace Netbaseteam\Marketplace\Model\Config\Source\Vacation;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Off')],
            ['value' => 1, 'label' => __('On')]
        ];
    }
}
