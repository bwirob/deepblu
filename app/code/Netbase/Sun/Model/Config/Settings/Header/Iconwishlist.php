<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Iconwishlist implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
			['value' => 'heart', 'label' => __('')], 
			['value' => 'heart-1', 'label' => __('')], 
			['value' => 'heart-2', 'label' => __('')],  
			['value' => 'heart-3', 'label' => __('')],
			['value' => 'heart-4', 'label' => __('')],
			['value' => 'heart-5', 'label' => __('')], 
			['value' => 'heart-6', 'label' => __('')],
			['value' => 'heart-7', 'label' => __('')] 
        ];
    }
}
