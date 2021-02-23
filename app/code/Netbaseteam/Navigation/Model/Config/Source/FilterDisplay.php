<?php
namespace Netbaseteam\Navigation\Model\Config\Source;

class FilterDisplay implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {

        return [
            ['value' => 1, 'label' => __('Scroll')],
            ['value' => 2, 'label' => __('More/Less')],
        ];
    }
}