<?php

namespace Netbase\Product\Model;

/**
 * Product Model
 *
 * @method \Netbase\Product\Model\Resource\Page _getResource()
 * @method \Netbase\Product\Model\Resource\Page getResource()
 */
class Typevalue extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbase\Product\Model\ResourceModel\Typevalue');
    }

}
