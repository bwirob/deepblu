<?php

/**
 * FAQ Resource Collection
 */
namespace Netbaseteam\Faq\Model\ResourceModel\Faqcategory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected $_idFieldName = 'faq_category_id';
    protected function _construct()
    {
        $this->_init('Netbaseteam\Faq\Model\Faqcategory', 'Netbaseteam\Faq\Model\ResourceModel\Faqcategory');
    }
}
