<?php
/**
 * Adminhtml product list block
 *
 */
namespace Netbase\Product\Block\Adminhtml;

class Product extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_product';
        $this->_blockGroup = 'Netbase_Product';
        $this->_headerText = __('Product');
        $this->_addButtonLabel = __('Add New Type');
        parent::_construct();
        if ($this->_isAllowedAction('Netbase_Product::save')) {
            $this->buttonList->update('add', 'label', __('Add New Type'));
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
