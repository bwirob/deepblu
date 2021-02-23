<?php
namespace Netbaseteam\Blog\Block\Adminhtml\Category\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        $model = $this->_coreRegistry->registry('category');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Netbaseteam_Blog::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('faq_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);

        if ($model->getBlogCategoryId()) {
            $fieldset->addField('blog_category_id', 'hidden', ['name' => 'blog_category_id']);
        }
        
        $fieldset->addField('url_rewrite_id', 'hidden', ['name' => 'url_rewrite_id']);
        $fieldset->addField('post_ids', 'hidden', ['name' => 'post_ids_callback']);

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('Identifier'),
                'title' => __('Identifier'),
                'required' => true,
                'class'=>'validate-xml-identifier',
                'disabled' => $isElementDisabled
            ]
        );

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'config' =>$wysiwygConfig

            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'options' => ['1' => __('Enable'), '0' => __('Disable')],
                'disabled' => $isElementDisabled
            ]
        );


        
        $fieldset->addField(
            'store_ids',
            'multiselect',
           [
             'name'     => 'store_ids[]',
             'label'    => __('Store Views'),
             'title'    => __('Store Views'),
             'required' => true,
             'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
           ]
        );



        $fieldset->addField(
            'ordering',
            'text',
            [
                'name' => 'ordering',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        
        $this->_eventManager->dispatch('adminhtml_category_edit_tab_main_prepare_form', ['form' => $form]);

       

        $dataForm = $model->getData();

        if(!empty($dataForm['category_image'])){
            $dataForm['category_image'] = 'blog/category_image/'. $dataForm['category_image'];
        }

        if(!empty($dataForm['thumbnail'])){
            $dataForm['thumbnail'] = 'blog/thumbnail/'. $dataForm['thumbnail'];
        }
    
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
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('General');
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

     protected function _getAdditionalElementTypes()
    {
        return ['image' => 'Netbaseteam\Blog\Block\Adminhtml\Form\Element\Image'];
    }
}
