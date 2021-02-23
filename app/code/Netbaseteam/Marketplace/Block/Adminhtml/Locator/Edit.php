<?php

namespace Netbaseteam\Marketplace\Block\Adminhtml\Locator;

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
     * Initialize Multipleorderemail edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Netbaseteam_Marketplace';
        $this->_controller = 'adminhtml_locator';

        parent::_construct();

        if ($this->_isAllowedAction('Netbaseteam_Marketplace::save')) {
            $this->buttonList->update('save', 'label', __('Save Locator'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_coreRegistry->registry('current_locator')->getId()) {
            $this->buttonList->remove('reset');
        }
        $this->buttonList->remove('delete');

    }

    /**
     * Retrieve text for header element depending on loaded blocklist
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_locator')->getId()) {
            return __("Edit Seller Locator '%1'", $this->escapeHtml($this->_coreRegistry->registry('current_locator')->getTitle()));
        } else {
            return __('New Seller Locator');
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
