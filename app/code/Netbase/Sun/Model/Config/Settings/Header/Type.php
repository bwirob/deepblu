<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'one', 'label' => __('Type 1')],
			['value' => 'three', 'label' => __('Type 2')],
			['value' => 'for', 'label' => __('Type 3')],
			['value' => 'five', 'label' => __('Type 4')],
			['value' => 'six', 'label' => __('Type 5')],
			['value' => 'seven', 'label' => __('Type 6')],
			['value' => 'eight', 'label' => __('Type 7')],
			['value' => 'nine', 'label' => __('Type 8')],
			['value' => 'ten', 'label' => __('Type 9')],
			['value' => 'eleven', 'label' => __('Type 10')]
        ];
    }
}
