<?php

/**
 * Categoryicon Resource Collection
 */
namespace Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cmsmart\Categoryicon\Model\Categoryicon', 'Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon');
    }
}
