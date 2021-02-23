<?php

/**
 * Productvideo Resource Collection
 */
namespace Netbaseteam\Productvideo\Model\ResourceModel\Productvideo;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
	protected $_idFieldName = 'productvideo_id';
	
    protected function _construct()
    {
        $this->_init('Netbaseteam\Productvideo\Model\Productvideo', 'Netbaseteam\Productvideo\Model\ResourceModel\Productvideo');
    }
}
