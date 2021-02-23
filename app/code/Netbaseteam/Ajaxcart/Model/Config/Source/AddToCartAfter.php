<?php
namespace  Netbaseteam\Ajaxcart\Model\Config\Source;

class AddToCartAfter implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Continue Shopping')], ['value' => 2, 'label' => __('Go to Shopping Cart')], ['value' => 3, 'label' => __('Show Confirmation Message')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [1 => __('Continue Shopping'), 2 => __('Go to Shopping Cart'),3 => __('Show Confirmation Message')];
    }
}
