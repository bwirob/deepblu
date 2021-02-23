<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Iconsearch implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'search', 'label' => __('')],
			['value' => 'search-1', 'label' => __('')],
			['value' => 'search-2', 'label' => __('')],
            ['value' => 'search-3', 'label' => __('')], 
			['value' => 'search-4', 'label' => __('')], 
			['value' => 'search-5', 'label' => __('')], 
			['value' => 'search-6', 'label' => __('')],	
			['value' => 'search-7', 'label' => __('')],
			['value' => 'search-8', 'label' => __('')]			
        ];
    }
}
