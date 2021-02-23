<?php
namespace Netbaseteam\Blog\Block\Adminhtml\Comment\Edit\Tab;

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
        $model = $this->_coreRegistry->registry('comment');

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

        $form->setHtmlIdPrefix('comment_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Comment Information')]);

        if ($model->getBlogCommentId()) {
            $fieldset->addField('blog_comment_id', 'hidden', ['name' => 'blog_comment_id']);
        }


        $fieldset->addField(
            'author_name',
            'text',
            [
                'name' => 'author_name',
                'label' => __('Author Name'),
                'title' => __('Author Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'author_email',
            'text',
            [
                'name' => 'author_email',
                'label' => __('Author Email'),
                'title' => __('Author Email'),
                'required' => true,
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
                'options' => ['2'=> __('Pending'),'1' => __('Enable'), '0' => __('Disable')],
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

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'label' => __('Content'),
                'title' => __('Content'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'config' =>$wysiwygConfig
            ]
        );

        
        
        $this->_eventManager->dispatch('adminhtml_comment_edit_tab_main_prepare_form', ['form' => $form]);

       

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
