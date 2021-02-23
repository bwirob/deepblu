<?php
/**
 * Adminhtml blog list block
 *
 */
namespace Netbaseteam\Blog\Block\Adminhtml;

class Blog extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_blog';
        $this->_blockGroup = 'Netbaseteam_Blog';
        $this->_headerText = __('Blog');
        $this->_addButtonLabel = __('Add New Blog');
        parent::_construct();
        if ($this->_isAllowedAction('Netbaseteam_Blog::save')) {
            $this->buttonList->update('add', 'label', __('Add New Blog'));
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
