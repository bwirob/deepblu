<?php

/**
 * Product Resource Collection
 */
namespace Netbase\Product\Model\ResourceModel\Typevalue;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbase\Product\Model\Typevalue', 'Netbase\Product\Model\ResourceModel\Typevalue');
    }
}
