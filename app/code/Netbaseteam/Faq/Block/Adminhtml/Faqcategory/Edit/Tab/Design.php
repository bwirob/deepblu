<?php
namespace Netbaseteam\Faq\Block\Adminhtml\Faqcategory\Edit\Tab;

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

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('faq_category');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Netbaseteam_Faq::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('faq_main_');

        $fieldset = $form->addFieldset('category_title_design', ['legend' => __('Category Title Option')]);

        $fieldset->addField('category_fontsize', 'text', array(
            'label'     => __('Category Font Size'),
            'required'  => false,
            'note'=>__('Unit: px'),
            'class' => 'validate-number',
            'name'      => 'category_fontsize',
        ));
        
        $fieldset->addField('category_color', 'text', array(
            'label'     => __('Category Title Color'),
            'class'  => 'jscolor {hash:true,refine:false}',
            'required'  => false,
            'name'      => 'category_color',
        ));

        

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Question Options')]);


        $fieldset->addField('fontsize', 'text', array(
            'label'     => __('Font Size'),
            'required'  => false,
            'note'=>__('Unit: px'),
            'class' => 'validate-number',
            'name'      => 'fontsize',
        ));
        
        $fieldset->addField('text_color', 'text', array(
            'label'     => __('Text Color'),
            'class'  => 'jscolor {hash:true,refine:false}',
            'required'  => false,
            'name'      => 'text_color',
        ));

        $fieldset->addField('background_color', 'text', array(
            'label'     => __('Background Color'),
            'class'  => 'jscolor {hash:true,refine:false}',
            'required'  => false,
            'name'      => 'background_color',
        ));

        $fieldset->addField('border_color', 'text', array(
            'label'     => __('Border Color'),
            'class'  => 'jscolor {hash:true,refine:false}',
            'required'  => false,
            'name'      => 'border_color',
        ));

        $fieldset->addField('border_width', 'text', array(
            'label'     => __('Border Width'),
            'required'  => false,
            'class' => 'validate-number',
            'name'      => 'border_width',
            'note'=>__('Unit: px'),
        ));


        $fieldset = $form->addFieldset('active_design', ['legend' => __('Question Active Option')]);
        
        $fieldset->addField('active_color', 'text', array(
            'label'     => __('Text Color(Active)'),
            'class'  => 'jscolor {hash:true,refine:false}',
            'required'  => false,
            'name'      => 'active_color',
        ));

        $fieldset->addField('active_background', 'text', array(
            'label'     => __('Background Color(Active)'),
            'class'  => 'jscolor {hash:true,refine:false}',
            'required'  => false,
            'name'      => 'active_background',
        ));

            
        $this->_eventManager->dispatch('adminhtml_faq_edit_tab_main_prepare_form', ['form' => $form]);


        $dataForm = $model->getData();
        
        $form->setValues($dataForm);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Design');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Design');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
