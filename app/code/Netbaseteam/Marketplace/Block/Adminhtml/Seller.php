<?php

namespace Netbaseteam\Marketplace\Block\Adminhtml;

class Seller extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
