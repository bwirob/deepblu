<?php

namespace Netbaseteam\Locator\Model;

/**
 * Blog Model
 *
 * @method \Netbaseteam\Blog\Model\Resource\Page _getResource()
 * @method \Netbaseteam\Blog\Model\Resource\Page getResource()
 */
class Locator extends \Magento\Framework\Model\AbstractModel
{
    
    protected function _construct()
    {
        $this->_init('Netbaseteam\Locator\Model\ResourceModel\Locator');
    }

}
