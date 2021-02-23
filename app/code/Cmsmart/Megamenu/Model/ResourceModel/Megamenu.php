<?php

namespace Cmsmart\Megamenu\Model\ResourceModel;

/**
 * Megamenu Resource Model
 */
class Megamenu extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_megamenu', 'megamenu_id');
    }
}
