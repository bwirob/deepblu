<?php
namespace Netbase\Sun\Model\Config\Settings\Footer;

class Footercf implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Type 1')]
			 , 
            ['value' => '2', 'label' => __('Type 2')], 
            ['value' => '3', 'label' => __('Type 3')], 
            ['value' => '4', 'label' => __('Type 4')], 
            ['value' => '5', 'label' => __('Type 5')], 
            ['value' => '6', 'label' => __('Type 6')], 
            ['value' => '7', 'label' => __('Type 7')] 
        ];
    }

    public function toArray()
    {
        return [
            '1' => __('Type 1')
			/* , 
            '2' => __('Type 2'), 
            '3' => __('Type 3'), 
            '4' => __('Type 4'), 
            '5' => __('Type 5'), 
            '6' => __('Type 6'), 
            '7' => __('Type 7') */
        ];
    }
}
