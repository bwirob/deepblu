<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Layouttypesearch implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'one', 'label' => __('Type 1')],
			['value' => 'two', 'label' => __('Type 2')],
			['value' => 'three', 'label' => __('Type 3')]
        ];
    }
}
