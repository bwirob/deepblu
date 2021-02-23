<?php
/**
 * Adminhtml megamenu list block
 *
 */
namespace Cmsmart\Megamenu\Block\Adminhtml;

class Megamenu extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_megamenu';
        $this->_blockGroup = 'Cmsmart_Megamenu';
        $this->_headerText = __('Megamenu');
        $this->_addButtonLabel = __('Add New Megamenu');
        parent::_construct();
        if ($this->_isAllowedAction('Cmsmart_Megamenu::save')) {
            $this->buttonList->update('add', 'label', __('Add New Megamenu'));
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
