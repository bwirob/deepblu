<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Position implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Left')],
            ['value' => '2', 'label' => __('Center')],
			['value' => '3', 'label' => __('Right')],
			['value' => '4', 'label' => __('Dont Show')]
        ];
    }
}
