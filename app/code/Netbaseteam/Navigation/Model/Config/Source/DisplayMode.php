<?php
namespace Netbaseteam\Navigation\Model\Config\Source;

class DisplayMode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {

        return [
            ['value' => 1, 'label' => __('Check Box')],
            ['value' => 2, 'label' => __('Drop-down')],
        ];
    }
}