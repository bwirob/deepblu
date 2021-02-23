<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Filter;

class SkipList extends \Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Grid\Filter\AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        return ['nin' => $this->getValue() ?: [0]];
    }
}
