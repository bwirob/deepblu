<?php

namespace Netbaseteam\Shopbybrand\Model;

/**
 * Shopbybrand Model
 *
 * @method \Netbaseteam\Shopbybrand\Model\Resource\Page _getResource()
 * @method \Netbaseteam\Shopbybrand\Model\Resource\Page getResource()
 */
class Shopbybrand extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand');
    }

}
