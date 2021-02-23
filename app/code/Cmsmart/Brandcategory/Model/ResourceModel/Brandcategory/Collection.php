<?php

/**
 * Brandcategory Resource Collection
 */
namespace Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cmsmart\Brandcategory\Model\Brandcategory', 'Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory');
    }
}
