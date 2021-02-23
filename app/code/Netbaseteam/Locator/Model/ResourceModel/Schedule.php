<?php

namespace Netbaseteam\Locator\Model\ResourceModel;

/**
 * FAQ Resource Model
 */
class Schedule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_schedule', 'schedule_id');
    }
}