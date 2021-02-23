<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Layouttype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'one', 'label' => __('Type 1')],
			['value' => 'two', 'label' => __('Type 2')]
        ];
    }
}
