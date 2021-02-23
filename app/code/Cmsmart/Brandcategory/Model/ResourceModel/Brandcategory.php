<?php

namespace Cmsmart\Brandcategory\Model\ResourceModel;

/**
 * Brandcategory Resource Model
 */
class Brandcategory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_brandcategory', 'brandcategory_id');
    }
}
