<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Product\Model\Config\Homepage;

class Section implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {        
        return [
            ['value' => '', 'label' => __('Select Content')],         
            ['value' => 'slider', 'label' => __('Slider')], 
            ['value' => 'policy', 'label' => __('Policy')],
            ['value' => 'banner', 'label' => __('Banner Images')],
            ['value' => 'bestseller', 'label' => __('Bestseller Product')],
            ['value' => 'new_product', 'label' => __('New Product')],
            ['value' => 'deal_product', 'label' => __('Deal Product')],
            ['value' => 'featured_product', 'label' => __('Featured Product')],
            ['value' => 'brand', 'label' => __('Brand Slider')],
            ['value' => 'custom_static_block', 'label' => __('Custom Static Block')]
        ];
    }
}
