<?php

namespace Netbaseteam\Orderupload\Model;

/**
 * Orderupload Model
 *
 * @method \Netbaseteam\Orderupload\Model\Resource\Page _getResource()
 * @method \Netbaseteam\Orderupload\Model\Resource\Page getResource()
 */
class Orderupload extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Orderupload\Model\ResourceModel\Orderupload');
    }

}
