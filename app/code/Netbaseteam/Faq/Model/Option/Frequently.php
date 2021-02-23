<?php
namespace Netbaseteam\Faq\Model\Option;

use Magento\Catalog\Model\Product;
use Magento\Framework\Data\OptionSourceInterface;

class Frequently implements OptionSourceInterface
{
	 public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('No')],['value' => 1, 'label' => __('Yes')]];
    }
}
