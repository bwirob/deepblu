<?php
/**
 * Adminhtml brandcategory list block
 *
 */
namespace Cmsmart\Brandcategory\Block\Adminhtml;

class Brandcategory extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_brandcategory';
        $this->_blockGroup = 'Cmsmart_Brandcategory';
        $this->_headerText = __('Brand Manager');
        $this->_addButtonLabel = __('Add New Brandcategory');
        parent::_construct();
        if ($this->_isAllowedAction('Cmsmart_Brandcategory::save')) {
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
