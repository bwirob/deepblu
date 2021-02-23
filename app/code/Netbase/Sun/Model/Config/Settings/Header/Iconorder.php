<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Iconorder implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [ 
			['value' => 'box', 'label' => __('')], 
			['value' => 'box-1', 'label' => __('')], 
			['value' => 'box-2', 'label' => __('')],  
			['value' => 'box-3', 'label' => __('')]
        ];
    }
}
