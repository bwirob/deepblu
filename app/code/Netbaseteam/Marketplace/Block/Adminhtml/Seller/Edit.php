<?php
namespace Netbaseteam\Marketplace\Block\Adminhtml\Seller;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Netbaseteam_Marketplace';
        $this->_controller = 'adminhtml_seller';

        parent::_construct();

        if ($this->_isAllowedAction('Netbaseteam_Marketplace::save')) {
            $this->buttonList->update('save', 'label', __('Update Commission'));
        } else {
            $this->buttonList->remove('save');
        }


        $this->buttonList->remove('delete');

        if ($this->_coreRegistry->registry('current_seller')->getId()) {
            $this->buttonList->remove('reset');
        }
    }

    /**
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_seller')->getId()) {
            return __("Edit Seller '%1'", $this->escapeHtml($this->_coreRegistry->registry('current_seller')->getTitle()));
        } else {
            return __('Add Commission');
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

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('marketplace/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}
