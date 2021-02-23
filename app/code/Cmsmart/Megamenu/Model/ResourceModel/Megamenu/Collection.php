<?php

/**
 * Megamenu Resource Collection
 */
namespace Cmsmart\Megamenu\Model\ResourceModel\Megamenu;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cmsmart\Megamenu\Model\Megamenu', 'Cmsmart\Megamenu\Model\ResourceModel\Megamenu');
    }
}
