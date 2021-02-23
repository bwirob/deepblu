<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Iconreviews implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [ 
			['value' => 'eye', 'label' => __('')], 
			['value' => 'eye-1', 'label' => __('')],  
			['value' => 'eye-2', 'label' => __('')],  
			['value' => 'eye-3', 'label' => __('')],  
			['value' => 'eye-4', 'label' => __('')],  
			['value' => 'eye-5', 'label' => __('')],  
			['value' => 'eye-6', 'label' => __('')],  
			['value' => 'eye-7', 'label' => __('')]
			 
        ];
    }
}
