<?php

/**
 * FAQ Resource Collection
 */
namespace Netbaseteam\Locator\Model\ResourceModel\Workdate;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected $_idFieldName = 'work_date_id';
    protected function _construct()
    {
        $this->_init('Netbaseteam\Locator\Model\Workdate', 'Netbaseteam\Locator\Model\ResourceModel\Workdate');
    }
}
