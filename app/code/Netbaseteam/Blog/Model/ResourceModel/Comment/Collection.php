<?php

/**
 * FAQ Resource Collection
 */
namespace Netbaseteam\Blog\Model\ResourceModel\Comment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected $_idFieldName = 'blog_comment_id';
    protected function _construct()
    {
        $this->_init('Netbaseteam\Blog\Model\Comment', 'Netbaseteam\Blog\Model\ResourceModel\Comment');
    }
}
