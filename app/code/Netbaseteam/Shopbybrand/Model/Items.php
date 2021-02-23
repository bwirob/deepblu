<?php
/**
 * Copyright © 2015 Netbaseteam. All rights reserved.
 */

namespace Netbaseteam\ShopByBrand\Model;

class Items extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Netbaseteam\ShopByBrand\Model\ResourceModel\ShopByBrand');
    }
}
