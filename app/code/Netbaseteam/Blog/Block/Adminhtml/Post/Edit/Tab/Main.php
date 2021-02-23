<?php
namespace Netbaseteam\Blog\Block\Adminhtml\Post\Edit\Tab;

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
        $model = $this->_coreRegistry->registry('post');

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

        if ($model->getPostId()) {
            $fieldset->addField('post_id', 'hidden', ['name' => 'post_id']);
        }

        $fieldset->addField('url_rewrite_id', 'hidden', ['name' => 'url_rewrite_id']);

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Post Title'),
                'title' => __('Post Title'),
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
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required'  => false,
                'note'=>__('Allowed file types: jpg, jpeg, gif, png'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'thumbnail',
            'image',
            [
                'name' => 'thumbnail',
                'label' => __('Thumbnail'),
                'title' => __('Thumbnail'),
                'required'  => false,
                'note'=>__('Allowed file types: jpg, jpeg, gif,png'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'feature_image',
            'image',
            [
                'name' => 'feature_image',
                'label' => __('Feature Image'),
                'title' => __('Feature Image'),
                'required'  => false,
                'note'=>__('Allowed file types: jpg, jpeg, gif,png'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'tag',
            'text',
            [
                'name' => 'tag',
                'label' => __('Tag Name'),
                'title' => __('Tag Name'),
                'note'=>__('Comma-separated.'),
                'class'=>'no-whitespace',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'enable_comment',
            'select',
            [
                'name' => 'enable_comment',
                'label' => __('Enable Comment'),
                'title' => __('Enable Comment'),
                'options' => ['1' => __('Enable'), '0' => __('Disable')],
                'disabled' => $isElementDisabled
            ]
        );


        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
        
        $fieldset->addField('creation_time', 'date', [
            'name'     => 'creation_time',
            'date_format' => $dateFormat,
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'value' => $model->getCreatedTime(),
            'label'    => __('Creation Time'),
            'title'    => __('Creation Time'),
            'required' => true
        ]);
 
        $this->_eventManager->dispatch('adminhtml_post_edit_tab_main_prepare_form', ['form' => $form]);

       

        $dataForm = $model->getData();

        if(!empty($dataForm['image'])){
            $dataForm['image'] = 'blog/image/'. $dataForm['image'];
        }

        if(!empty($dataForm['thumbnail'])){
            $dataForm['thumbnail'] = 'blog/thumbnail/'. $dataForm['thumbnail'];
        }

        if(!empty($dataForm['feature_image'])){
            $dataForm['feature_image'] = 'blog/feature_image/'. $dataForm['feature_image'];
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
