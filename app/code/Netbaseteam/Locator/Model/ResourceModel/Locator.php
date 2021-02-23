<?php

namespace Netbaseteam\Locator\Model\ResourceModel;

/**
 * FAQ Resource Model
 */
class Locator extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_localtor', 'localtor_id');
    }
}
