<?php
namespace Netbaseteam\Locator\Model\Config\System;

class Unit implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [['value' => 'km', 'label' => __('Kilometres')], ['value' => 'mile', 'label' => __('Miles')]];
    }

    
}
