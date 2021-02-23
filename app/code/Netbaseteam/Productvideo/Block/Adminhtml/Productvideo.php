<?php
/**
 * Adminhtml productvideo list block
 *
 */
namespace Netbaseteam\Productvideo\Block\Adminhtml;

class Productvideo extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_productvideo';
        $this->_blockGroup = 'Netbaseteam_Productvideo';
        $this->_headerText = __('Productvideo');
        $this->_addButtonLabel = __('Add New Productvideo');
        parent::_construct();
        if ($this->_isAllowedAction('Netbaseteam_Productvideo::save')) {
            $this->buttonList->update('add', 'label', __('Add New Productvideo'));
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
