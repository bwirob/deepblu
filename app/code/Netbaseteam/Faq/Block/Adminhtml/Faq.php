<?php
/**
 * Adminhtml faq list block
 *
 */
namespace Netbaseteam\Faq\Block\Adminhtml;

class Faq extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_faq';
        $this->_blockGroup = 'Netbaseteam_Faq';
        $this->_headerText = __('Faq');
        $this->_addButtonLabel = __('Add New Faq');
        parent::_construct();
        if ($this->_isAllowedAction('Netbaseteam_Faq::save')) {
            $this->buttonList->update('add', 'label', __('Add New Faq'));
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
