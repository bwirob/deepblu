<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Iconnewsletter implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [ 
			['value' => 'mail', 'label' => __('')],  
			['value' => 'mail-1', 'label' => __('')],  
			['value' => 'mail-2', 'label' => __('')],  
			['value' => 'mail-3', 'label' => __('')],  
			['value' => 'mail-4', 'label' => __('')],  
			['value' => 'mail-5', 'label' => __('')],
			['value' => 'mail-6', 'label' => __('')],
			['value' => 'mail-7', 'label' => __('')], 
			['value' => 'mail-8', 'label' => __('')], 			
			['value' => 'mail-alt', 'label' => __('')]
        ];
    }
}
