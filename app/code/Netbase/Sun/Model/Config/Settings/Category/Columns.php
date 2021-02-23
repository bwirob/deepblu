<?php
namespace Netbase\Sun\Model\Config\Settings\Category;

class Columns implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => '3', 'label' => __('3 Columns')], ['value' => '4', 'label' => __('4 Columns')]];
    }

    public function toArray()
    {
        return ['3' => __('3 Columns'), '4' => __('4 Columns')];
    }
}
