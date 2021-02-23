<?php
namespace Netbase\Sun\Model\Config\Settings\Header;

class Iconbill implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [ 
			['value' => 'money', 'label' => __('')],  
			['value' => 'money-1', 'label' => __('')],  
			['value' => 'money-2', 'label' => __('')],  
			['value' => 'mastercard', 'label' => __('')],  
			['value' => 'visa', 'label' => __('')],  
			['value' => 'discover', 'label' => __('')],  
			['value' => 'ok-squared', 'label' => __('')],  
			['value' => 'cc-paypal', 'label' => __('')] 
        ];
    }
}
