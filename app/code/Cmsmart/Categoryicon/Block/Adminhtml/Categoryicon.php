<?php
/**
 * Adminhtml categoryicon list block
 *
 */
namespace Cmsmart\Categoryicon\Block\Adminhtml;

class Categoryicon extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_categoryicon';
        $this->_blockGroup = 'Cmsmart_Categoryicon';
        $this->_headerText = __('Category Icon Manager');
        $this->_addButtonLabel = __('Add New Categoryicon');
        parent::_construct();
		$this->buttonList->remove('add');
        if ($this->_isAllowedAction('Cmsmart_Categoryicon::save')) {
            $this->buttonList->update('add', 'label', __('Add New Categoryicon'));
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
