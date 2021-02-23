<?php

/**
 * FAQ Resource Collection
 */
namespace Netbaseteam\Faq\Model\ResourceModel\Faq;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected $_idFieldName = 'faq_id';
    protected function _construct()
    {
        $this->_init('Netbaseteam\Faq\Model\Faq', 'Netbaseteam\Faq\Model\ResourceModel\Faq');
    }
}
