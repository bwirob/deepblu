<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Adminhtml\Seller\Edit;

/**
 * Adminhtml seller edit form
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
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
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
        $this->setId('seller_form');
        $this->setTitle(__('Commission Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Netbaseteam\Marketplace\Model\Seller $model */
        $model = $this->_coreRegistry->registry('current_seller');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('seller_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Commission Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'commission_amount',
            'text',
            [
                'name' => 'commission_amount',
                'label' => __('Amount'),
                'title' => __('Amount')
            ]
        );
        $fieldset->addField(
            'fixed_or_percentage',
            'select',
            [
                'label' => __('Fixed or Percentage'),
                'title' => __('Fixed or Percentage'),
                'name' => 'fixed_or_percentage',
                'required' => true,
                'options' => ['0' => __('Fixed'), '1' => __('Percentage')]
            ]
        );
        $fieldset->addField(
            'commission_type',
            'select',
            [
                'label' => __('Type'),
                'title' => __('Type'),
                'name' => 'commission_type',
                'required' => true,
                'options' => ['0' => __('Per Item'), '1' => __('Per Order')]
            ]
        );
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}