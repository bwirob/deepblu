<?php
/**
 * Adminhtml shopbybrand list block
 *
 */
namespace Netbaseteam\Shopbybrand\Block\Adminhtml;

class Shopbybrand extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_shopbybrand';
        $this->_blockGroup = 'Netbaseteam_Shopbybrand';
        $this->_headerText = __('Brand');
        $this->_addButtonLabel = __('Add New Brand');
        parent::_construct();
        if ($this->_isAllowedAction('Netbaseteam_Shopbybrand::save')) {
            $this->buttonList->update('add', 'label', __('Add New Brand'));
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
