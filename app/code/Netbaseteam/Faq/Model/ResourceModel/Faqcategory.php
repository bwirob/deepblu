<?php

namespace Netbaseteam\Faq\Model\ResourceModel;

/**
 * FAQ Resource Model
 */
class Faqcategory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_faq_category', 'faq_category_id');
    }
}
