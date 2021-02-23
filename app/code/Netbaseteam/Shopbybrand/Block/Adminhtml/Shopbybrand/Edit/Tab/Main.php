<?php
namespace Netbaseteam\Shopbybrand\Block\Adminhtml\Shopbybrand\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

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
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        \Netbaseteam\Shopbybrand\Helper\Data $shopbybrandHelper,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_shopbybrandHelper = $shopbybrandHelper;
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
        $model = $this->_coreRegistry->registry('shopbybrand');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Netbaseteam_Shopbybrand::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('shopbybrand_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);

        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );

        $imageUrl = $mediaDirectory . 'shopbybrand' . $model->getLogo();

        if ($model->getId()) {
            $fieldset->addField('brand_id', 'hidden', ['name' => 'brand_id']);
            $model['store_ids'] = explode(',', $model['store_ids']);
        }

        $fieldset->addField('url_rewrite_id', 'hidden', ['name' => 'url_rewrite_id']);

        $fieldset->addField(
            'brand_title',
            'text',
            [
                'name' => 'brand_title',
                'label' => __('Brand Title'),
                'title' => __('Shopbybrand Title'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'urlkey',
            'text',
            [
                'name' => 'urlkey',
                'label' => __('Urlkey'),
                'title' => __('Urlkey'),
                'required' => true,
                'class'=>'validate-xml-identifier',
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
            'logo', 'image', [
            'name' => 'logo',
            'label' => __('Logo Image'),
            'note'=>__('Allowed file types: jpg, jpeg, gif, png'),
            
            'title' => __('Logo Image')

            ]
        );

        $fieldset->addField(
            'banner',
            'image',
            [
                'name' => 'banner',
                'note'=>__('Allowed file types: jpg, jpeg, gif, png'),
                'label' => __('Banner'),
                'title' => __('Banner'),
                
            ]
        );

        $fieldset->addField(
           'store_ids',
           'multiselect',
               [
                 'name'     => 'store_ids',
                 'label'    => __('Store Views'),   
                 'title'    => __('Store Views'),
                 'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
                 'required' => true,
               ]
        );

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
        $contentField = $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'style' => 'height:10em',
                'label' => __('Description'),
                'title' => __('Description'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig,
                
            ]
        );

        $fieldset->addField(
            'meta_keyworlds',
            'textarea',
            [
                'name' => 'meta_keyworlds',
                'label' => __('Meta Keywords'),
                'title' => __('Meta Keywords'),
                
                'disabled' => $isElementDisabled
            ]
        );

         $fieldset->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Meta Description'),
                'title' => __('Meta Description'),
                
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'featured',
            'select',
            [
                'name' => 'featured',
                'label' => __('Is Featured Brand'),
                'title' => __('Is Featured Brand'),
                'options' => ['1' => __('Yes'), '0' => __('No')],
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_shopbybrand_edit_tab_main_prepare_form', ['form' => $form]);
         
        
        $dataForm = $model->getData();

        if(!empty($dataForm['logo'])){
            $dataForm['logo'] = 'Shopbybrand/'. $dataForm['logo'];
        }

        if(!empty($dataForm['banner'])){
            $dataForm['banner'] = 'Shopbybrand/'. $dataForm['banner'];
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
        return __('General Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('General Information');
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

    // protected function _getAdditionalElementTypes()
    // {
    //     return ['logo' => 'Netbaseteam\Shopbybrand\Block\Adminhtml\Form\Element\Image'];
    // }

    protected function getImageHtml($field, $image)
    {
        $html = '';
        if ($image) {
            $html .= '<p style="margin-top: 5px">';
            $html .= '<image style="min-width:300px;" src="' . $this->_shopbybrandHelper->getImageUrl($image) . '" />';
            $html .= '<input type="hidden" value="' . $image . '" name="old_' . $field . '"/>';
            $html .= '</p>';
        }
        return $html;
    }
}
