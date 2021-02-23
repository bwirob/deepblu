<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Iconaccount implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
			['value' => 'user', 'label' => __('')],
			['value' => 'user-1', 'label' => __('')], 
			['value' => 'user-2', 'label' => __('')], 
			['value' => 'user-3', 'label' => __('')],  
			['value' => 'user-4', 'label' => __('')], 
            ['value' => 'user-5', 'label' => __('')],
			['value' => 'user-6', 'label' => __('')],
			['value' => 'user-7', 'label' => __('')],
			['value' => 'user-secret', 'label' => __('')], 
			['value' => 'user-female', 'label' => __('')], 
			['value' => 'user-male', 'label' => __('')]			 
        ];
    }
}
