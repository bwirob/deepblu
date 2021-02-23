<?php

/**
 * FAQ Resource Collection
 */
namespace Netbaseteam\Blog\Model\ResourceModel\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected $_idFieldName = 'blog_category_id';
    protected function _construct()
    {
        $this->_init('Netbaseteam\Blog\Model\Category', 'Netbaseteam\Blog\Model\ResourceModel\Category');
    }
}
