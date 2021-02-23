<?php
namespace Netbaseteam\Locator\Block\Adminhtml\Locator\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Map extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_countryOption;

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
        \Netbaseteam\Locator\Model\Config\Source\Country $countryOption,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_countryOption = $countryOption;
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
        $model = $this->_coreRegistry->registry('localtor');

        
        if ($this->_isAllowedAction('Netbaseteam_Locator::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('localtor_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Location Information ')]);

        if ($model->getId()) {
            $fieldset->addField('localtor_id', 'hidden', ['name' => 'localtor_id']);
        }

        $fieldset->addField(
            'address',
            'text',
            [
                'name' => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $this->_countryOption->toOptionArray(),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'state',
            'text',
            [
                'name' => 'state',
                'label' => __('State/Province'),
                'title' => __('State/Province'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'zip_code',
            'text',
            [
                'name' => 'zip_code',
                'label' => __('Zip Code'),
                'title' => __('Zip Code'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        


        $fieldset->addField(
            'latitude',
            'text',
            [
                'name' => 'latitude',
                'label' => __('Latitude'),
                'title' => __('Latitude'),
                'required' => true,
                'class' => 'validate-number',
                
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'longitude',
            'text',
            [
                'name' => 'longitude',
                'label' => __('Longitude'),
                'title' => __('Longitude'),
                'required' => true,
                'class' => 'validate-number',
                'disabled' => $isElementDisabled
            ]
        );


        $fieldset->addField(
            'zoom_level',
            'text',
            [
                'name' => 'zoom_level',
                'label' => __('Zoom Level'),
                'title' => __('Zoom Level'),
                'required' => true,
                'class' => 'validate-number',
                'disabled' => $isElementDisabled
            ]
        );

        
        $renderer = $this->getLayout()->createBlock(
            'Netbaseteam\Locator\Block\Adminhtml\Form\Edit\Renderer'

        )->setTemplate(
            'Netbaseteam_Locator::add_map.phtml'
        );
        $fieldset->setRenderer($renderer);
        
        $this->_eventManager->dispatch('adminhtml_localtor_edit_tab_main_prepare_form', ['form' => $form]);

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
        return __('Google Map Location');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Location Information');
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
