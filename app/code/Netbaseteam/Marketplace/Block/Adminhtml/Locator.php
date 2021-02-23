<?php

namespace Netbaseteam\Marketplace\Block\Adminhtml;

class Locator extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_locator';
        $this->_blockGroup = 'Netbaseteam_Marketplace';
        $this->_headerText = __('Manage Locator');

        parent::_construct();

        if ($this->_isAllowedAction('Netbaseteam_Marketplace::save')) {
            $this->buttonList->update('add', 'label', __('Add New Locator'));
        } else {
            $this->buttonList->remove('add');
        }
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
