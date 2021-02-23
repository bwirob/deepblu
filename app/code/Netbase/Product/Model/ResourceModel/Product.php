<?php

namespace Netbase\Product\Model\ResourceModel;

/**
 * Product Resource Model
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('netbase_product_sectiontype', 'id');
    }
}
