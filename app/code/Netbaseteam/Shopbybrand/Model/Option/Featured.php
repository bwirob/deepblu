<?php
namespace Netbaseteam\Shopbybrand\Model\Option;

use Magento\Catalog\Model\Product;
use Magento\Framework\Data\OptionSourceInterface;

class Featured implements OptionSourceInterface
{
	 public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('Disable')],['value' => 1, 'label' => __('Enable')]];
    }
}
