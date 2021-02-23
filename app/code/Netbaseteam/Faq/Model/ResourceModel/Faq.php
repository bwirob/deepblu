<?php

namespace Netbaseteam\Faq\Model\ResourceModel;

/**
 * FAQ Resource Model
 */
class Faq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_faq', 'faq_id');
    }
}
