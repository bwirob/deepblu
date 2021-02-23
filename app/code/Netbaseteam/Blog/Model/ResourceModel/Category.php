<?php

namespace Netbaseteam\Blog\Model\ResourceModel;

/**
 * FAQ Resource Model
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmsmart_blog_category', 'blog_category_id');
    }
}
