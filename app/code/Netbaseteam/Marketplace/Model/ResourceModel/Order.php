<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

/**
 * netbaseteam_marketplace_order mysql resource
 */
class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('netbaseteam_marketplace_order', 'id');
    }
}
