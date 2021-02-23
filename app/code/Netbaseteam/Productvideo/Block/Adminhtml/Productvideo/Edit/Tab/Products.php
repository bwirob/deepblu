<?php
namespace Netbaseteam\Productvideo\Block\Adminhtml\Productvideo\Edit\Tab;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Products extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Initialise form fields
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(['data' => ['html_id_prefix' => 'productvideo_image_']]);

        $model = $this->_coreRegistry->registry('productvideo');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Netbaseteam_Productvideo::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        
        $layoutFieldset = $form->addFieldset(
            'image_fieldset',
            ['legend' => __('Please Assign This Video For Products'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );

        /* $layoutFieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required'  => false,
                'disabled' => $isElementDisabled
            ]
        ); */
		
		$field = $layoutFieldset->addField(
            'product_ids',
            'text',
            [
                'name' => 'product_ids',
                'label' => __('Products'),
                'title' => __('Products'),
            ]
        );
		$renderer = $this->getLayout()->createBlock(
			   'Netbaseteam\Productvideo\Block\Adminhtml\Renderer\Products'
		);
		$field->setRenderer($renderer);

        $this->_eventManager->dispatch('adminhtml_productvideo_edit_tab_image_prepare_form', ['form' => $form]);

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
        return __('Select Products');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Products');
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
    
    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return ['image' => 'Netbaseteam\Productvideo\Block\Adminhtml\Form\Element\Image'];
    }
}
