<?php

namespace Cmsmart\Megamenu\Model;

/**
 * Megamenu Model
 *
 * @method \Cmsmart\Megamenu\Model\Resource\Page _getResource()
 * @method \Cmsmart\Megamenu\Model\Resource\Page getResource()
 */
class Megamenu extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cmsmart\Megamenu\Model\ResourceModel\Megamenu');
    }

}
