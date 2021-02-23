<?php

namespace Netbaseteam\Orderupload\Model\ResourceModel;

/**
 * Orderupload Resource Model
 */
class Orderupload extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_orderupload', 'orderupload_id');
    }
}
