<?php
/**
 * Adminhtml product list block
 *
 */
namespace Netbase\Product\Block\Adminhtml;

class Homecontent extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_homecontent';
        $this->_blockGroup = 'Netbase_Product';
        $this->_headerText = __('Product');
        $this->_addButtonLabel = __('Add New Type Content');
        parent::_construct();
		$this->buttonList->remove('add');
        /* if ($this->_isAllowedAction('Netbase_Product::save')) {
            $this->buttonList->update('add', 'label', __('Add New Type Content'));
        } else {
            $this->buttonList->remove('add');
        } */
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
