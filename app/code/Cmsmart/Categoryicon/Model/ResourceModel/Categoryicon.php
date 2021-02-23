<?php

namespace Cmsmart\Categoryicon\Model\ResourceModel;

/**
 * Categoryicon Resource Model
 */
class Categoryicon extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_categoryicon', 'categoryicon_id');
    }
}
