<?php

namespace Netbaseteam\Faq\Model;

/**
 * FAQ Model
 *
 * @method \Netbaseteam\FAQ\Model\Resource\Page _getResource()
 * @method \Netbaseteam\FAQ\Model\Resource\Page getResource()
 */
class Faq extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Netbaseteam\Faq\Model\ResourceModel\Faq');
    }

}
