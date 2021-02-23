<?php


namespace Netbaseteam\Blog\Model\Config\Source;

class Liststyle implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'list', 'label' => __('List Style')],
            ['value' => 'grid-2', 'label' => __('2 Collumns Grid')],
            ['value' => 'grid-3', 'label' => __('3 Collumns Grid')]
        ];
    }

    
}
