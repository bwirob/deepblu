<?php
namespace Netbaseteam\Faq\Block\Adminhtml\Faq\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        $model = $this->_coreRegistry->registry('faq');

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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);

        if ($model->getId()) {
            $fieldset->addField('faq_id', 'hidden', ['name' => 'faq_id']);
        }


        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
            'question',
            'editor',
            [
                'name' => 'question',
                'label' => __('Question'),
                'title' => __('Question'),
                'required' => true,

                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'options' => ['2' => __('Pending'),'1' => __('Enable'), '0' => __('Disable')],
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
            'most_frequently',
            'select',
            [
                'name' => 'most_frequently',
                'label' => __('Most FAQ'),
                'title' => __('Most FAQ'),
                'options' => ['1' => __('Yes'), '0' => __('No')],
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'sidebar_faq',
            'select',
            [
                'name' => 'sidebar_faq',
                'label' => __('Sidebar Question'),
                'title' => __('Sidebar Question'),
                'options' => ['1' => __('Yes'), '0' => __('No')],
                'disabled' => $isElementDisabled
            ]
        );

       

        $fieldset->addField(
            'ordering',
            'text',
            [
                'name' => 'ordering',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'class' => 'validate-number',
                'disabled' => $isElementDisabled
            ]
        );

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
        
        $fieldset->addField('created_time', 'date', [
            'name'     => 'created_time',
            'date_format' => $dateFormat,
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'value' => $model->getCreatedTime(),
            'label'    => __('Created Date'),
            'title'    => __('Created Date'),
            'required' => true
        ]);

        
        
        $this->_eventManager->dispatch('adminhtml_faq_edit_tab_main_prepare_form', ['form' => $form]);
        
        $form->setValues($model->getData());
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
        return ['icon' => 'Netbaseteam\Faq\Block\Adminhtml\Form\Element\Image'];
    }
}
