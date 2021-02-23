<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Adminhtml\Locator\Edit;

/**
 * Adminhtml locator edit form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Netbaseteam\Marketplace\Model\Config\Source\Locator\SellerList $sellerList,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_sellerList = $sellerList;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('locator_form');
        $this->setTitle(__('Locator Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Netbaseteam\Marketplace\Model\Location $model */
        $model = $this->_coreRegistry->registry('current_locator');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('locator_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Locator Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'seller_id',
            'select',
            [
                'label' => __('Seller Shop Name'),
                'title' => __('Seller Shop Name'),
                'name' => 'seller_id',
                'required' => true,
                'values' => $this->_sellerList->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => false,
                'options' => ['1' => __('Enable'), '2' => __('Disable')],
            ]
        );

        $fieldset->addField(
            'shop_location',
            'text',
            [
                'name' => 'shop_location',
                'required' => true,
                'label' => __('Address'),
                'title' => __('Address')
            ]
        );

        $fieldset->addField(
            'shop_zipcode',
            'text',
            [
                'name' => 'shop_zipcode',
                'required' => false,
                'label' => __('Zip Code'),
                'title' => __('Zip Code')
            ]
        );

        $fieldset->addField(
            'shop_latitude',
            'text',
            [
                'label' => __('Latitude'),
                'title' => __('Latitude'),
                'name' => 'shop_latitude',
                'required' => true,
                'readonly' => true
            ]
        );

        $fieldset->addField(
            'shop_longitude',
            'text',
            [
                'label' => __('Longitude'),
                'title' => __('Longitude'),
                'name' => 'shop_longitude',
                'required' => true,
                'readonly' => true
            ]
        );

        $fieldset->addField(
            'zoom_level',
            'text',
            [
                'label' => __('Zoom Level'),
                'title' => __('Zoom Level'),
                'name' => 'zoom_level',
                'required' => true,
                'readonly' => true
            ]
        );

        $renderer = $this->getLayout()->createBlock(
            'Netbaseteam\Marketplace\Block\Adminhtml\Locator\Edit\Renderer'
        )->setTemplate(
            'Netbaseteam_Marketplace::locator/add_map.phtml'
        );
        $fieldset->setRenderer($renderer);

        $this->_eventManager->dispatch('adminhtml_locator_edit_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}