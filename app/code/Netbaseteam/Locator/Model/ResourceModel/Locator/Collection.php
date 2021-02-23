<?php

namespace Netbaseteam\Locator\Model\ResourceModel\Locator;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected $_idFieldName = 'localtor_id';
    protected function _construct()
    {
        $this->_init('Netbaseteam\Locator\Model\Locator', 'Netbaseteam\Locator\Model\ResourceModel\Locator');
    }
}
