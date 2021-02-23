<?php
namespace Netbaseteam\Blog\Block\Adminhtml\Category\Edit\Tab;

class Design extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
  
    protected $_systemStore;

    protected $_wysiwygConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

   
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('category');

        
        if ($this->_isAllowedAction('Netbaseteam_Blog::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('faq_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);

        if ($model->getBlogCategoryId()) {
            $fieldset->addField('blog_category_id', 'hidden', ['name' => 'blog_category_id']);
        }

      
        
        $this->_eventManager->dispatch('adminhtml_category_edit_tab_main_prepare_form', ['form' => $form]);

       

        $dataForm = $model->getData();

        if(!empty($dataForm['image'])){
            $dataForm['image'] = 'blog/image/'. $dataForm['image'];
        }

        if(!empty($dataForm['thumbnail'])){
            $dataForm['thumbnail'] = 'blog/thumbnail/'. $dataForm['thumbnail'];
        }
    
        $form->setValues($dataForm);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    
    public function getTabLabel()
    {
        return __('Design');
    }

    
    public function getTabTitle()
    {
        return __('Design');
    }

    
    public function canShowTab()
    {
        return true;
    }

    
    public function isHidden()
    {
        return false;
    }

    
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

     protected function _getAdditionalElementTypes()
    {
        return ['image' => 'Netbaseteam\Blog\Block\Adminhtml\Form\Element\Image'];
    }
}
