<?php
namespace Cmsmart\Brandcategory\Block\Adminhtml\Brandcategory\Edit\Tab;

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
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
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
		$wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
		
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('brandcategory');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Cmsmart_Brandcategory::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('brandcategory_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Brand Information')]);

        if ($model->getId()) {
            $fieldset->addField('brandcategory_id', 'hidden', ['name' => 'brandcategory_id']);
        }

        $fieldset->addField(
            'brand_name',
            'text',
            [
                'name' => 'brand_name',
                'label' => __('Brand Name'),
                'title' => __('Brand Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'logo',
            'image',
            [
                'name' => 'logo',
                'label' => __('Brand Logo'),
                'title' => __('Brand Logo'),
                'required'  => false,
                'disabled' => $isElementDisabled
            ]
        );
		
		$field = $fieldset->addField(
            'products',
            'text',
            [
                'name' => 'products',
                'label' => __('Products'),
                'title' => __('Products'),
            ]
        );
		$renderer = $this->getLayout()->createBlock(
			   'Cmsmart\Brandcategory\Block\Adminhtml\Renderer\Products'
		);
		$field->setRenderer($renderer);

        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => false,
				'config' => $wysiwygConfig,
				'style' => 'height:200px;',
                'disabled' => $isElementDisabled
            ]
        );
       
		$fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        
		/* show image in form */

		if ($model->getLogo()) {
			$path = 'Brandcategory/'.$model->getLogo();
			$model->setData('logo', $path);
		}
		
        $this->_eventManager->dispatch('adminhtml_brandcategory_edit_tab_main_prepare_form', ['form' => $form]);

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
        return __('Brand');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Brand');
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
