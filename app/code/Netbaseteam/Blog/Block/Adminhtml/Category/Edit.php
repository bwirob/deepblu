<?php
namespace Netbaseteam\Blog\Block\Adminhtml\Category;


class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
   
    protected $_coreRegistry = null;

    
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    
    protected function _construct()
    {
        $this->_objectId = 'blog_category_id';
        $this->_blockGroup = 'Netbaseteam_Blog';
        $this->_controller = 'adminhtml_category';
        parent::_construct();

        if ($this->_isAllowedAction('Netbaseteam_Blog::save')) {
            $this->buttonList->update('save', 'label', __('Save Category'));
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

        if ($this->_isAllowedAction('Netbaseteam_Blog::category_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Category'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('category')->getId()) {
            return __("Edit Category '%1'", $this->escapeHtml($this->_coreRegistry->registry('category')->getName()));
        } else {
            return __('New Category');
        }
    }

    
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('category/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }

    
}
