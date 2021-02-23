<?php

/**
 * FAQ Resource Collection
 */
namespace Netbaseteam\Blog\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected $_idFieldName = 'post_id';
    protected function _construct()
    {
        $this->_init('Netbaseteam\Blog\Model\Post', 'Netbaseteam\Blog\Model\ResourceModel\Post');
    }
}
