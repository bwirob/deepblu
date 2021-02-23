<?php
namespace Netbaseteam\Faq\Model\Option;

use Magento\Catalog\Model\Product;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
	 public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('Disable')],['value' => 1, 'label' => __('Enable')],
        ['value' => 2, 'label' => __('Pending')]];
    }
}




