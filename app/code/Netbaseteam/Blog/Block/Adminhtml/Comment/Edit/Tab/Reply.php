<?php
namespace Netbaseteam\Blog\Block\Adminhtml\Comment\Edit\Tab;

class Reply extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;


    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
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

       
        if ($this->_isAllowedAction('Netbaseteam_Blog::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('comment_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Reply Information')]);

        if ($model->getId()) {
            $fieldset->addField('blog_comment_id', 'hidden', ['name' => 'blog_comment_id']);
        }

        $fieldset->addField(
            'reply_author',
            'text',
            [
                'name' => 'reply_author',
                'label' => __('Reply Author'),
                'title' => __('Reply Author'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );


        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
            'reply_content',
            'editor',
            [
                'name' => 'reply_content',
                'label' => __('Reply Content'),
                'title' => __('Reply Content'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'config' =>$wysiwygConfig

            ]
        );

        
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
        

        $fieldset->addField(
            'reply_createtime',
            'date',
            [
                'name' => 'reply_createtime',
                'date_format' => $dateFormat,
                'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
                'value' => $model->getReplyCreatetime(),
                'label'    => __('Reply Create Time'),
                'title'    => __('Reply Create Time'),
                'required' => false
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
        return __('Reply Comment');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Reply Information');
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
        return ['icon' => 'Netbaseteam\Blog\Block\Adminhtml\Form\Element\Image'];
    }
}
