<?php

namespace Netbaseteam\Productvideo\Model\ResourceModel;

/**
 * Productvideo Resource Model
 */
class Productvideo extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_productvideo', 'productvideo_id');
    }
}
