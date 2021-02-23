<?php
namespace Netbase\Sun\Model\Config\Settings\Footer\Top;

class FooterBlockTop implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => '', 'label' => __('Do not show')], ['value' => 'custom', 'label' => __('Custom Block')]];
    }

    public function toArray()
    {
        return ['' => __('Do not show'), 'custom' => __('Custom Block')];
    }
}
