<?php

/**
 * Product Resource Collection
 */
namespace Netbase\Product\Model\ResourceModel\Product;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbase\Product\Model\Product', 'Netbase\Product\Model\ResourceModel\Product');
    }
}
