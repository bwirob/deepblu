<?php

namespace Netbaseteam\Blog\Model;

/**
 * Blog Model
 *
 * @method \Netbaseteam\Blog\Model\Resource\Page _getResource()
 * @method \Netbaseteam\Blog\Model\Resource\Page getResource()
 */
class Category extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Blog\Model\ResourceModel\Category');
    }

}
