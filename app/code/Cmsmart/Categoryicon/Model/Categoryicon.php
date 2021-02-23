<?php

namespace Cmsmart\Categoryicon\Model;

/**
 * Categoryicon Model
 *
 * @method \Cmsmart\Categoryicon\Model\Resource\Page _getResource()
 * @method \Cmsmart\Categoryicon\Model\Resource\Page getResource()
 */
class Categoryicon extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon');
    }

}
