<?php

namespace Netbaseteam\Shopbybrand\Model\ResourceModel;

/**
 * Shopbybrand Resource Model
 */
class Shopbybrand extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_shopbybrand', 'brand_id');
    }
}
