<?php

/**
 * Shopbybrand Resource Collection
 */
namespace Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected $_idFieldName = 'brand_id';
    protected function _construct()
    {
        $this->_init('Netbaseteam\Shopbybrand\Model\Shopbybrand', 'Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand');
    }
}
