<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Headerelement implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
			['value' => '0', 'label' => __('Language')],
            ['value' => '1', 'label' => __('Currency')],
            ['value' => '2', 'label' => __('Search')], 
			['value' => '3', 'label' => __('Cart')],
			['value' => '4', 'label' => __('User')], 
			['value' => '5', 'label' => __('Logo')], 
			['value' => '6', 'label' => __('Menu')],
			['value' => '7', 'label' => __('Static Block')]
        ];
    }
}
