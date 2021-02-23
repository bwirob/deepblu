<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

/**
 * netbaseteam_marketplace_attribute_set mysql resource
 */
class AttributeSet extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('netbaseteam_marketplace_attribute_set', 'id');
    }
}
