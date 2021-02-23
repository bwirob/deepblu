<?php

/**
 * Orderupload Resource Collection
 */
namespace Netbaseteam\Orderupload\Model\ResourceModel\Orderupload;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Orderupload\Model\Orderupload', 'Netbaseteam\Orderupload\Model\ResourceModel\Orderupload');
    }
}
