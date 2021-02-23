<?php

/**
 * FAQ Resource Collection
 */
namespace Netbaseteam\Locator\Model\ResourceModel\Schedule;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected $_idFieldName = 'schedule_id';
    protected function _construct()
    {
        $this->_init('Netbaseteam\Locator\Model\Schedule', 'Netbaseteam\Locator\Model\ResourceModel\Schedule');
    }
}
