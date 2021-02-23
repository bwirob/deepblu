<?php
namespace Netbaseteam\Locator\Block\Adminhtml;

class Locator extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_locator';
        $this->_blockGroup = 'Netbaseteam_Locator';
        $this->_headerText = __('Locator');
        $this->_addButtonLabel = __('Add New Locator');
        parent::_construct();
        if ($this->_isAllowedAction('Netbaseteam_Locator::save')) {
            $this->buttonList->update('add', 'label', __('Add New Locator'));
        } else {
            $this->buttonList->remove('add');
        }
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
