<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Bundle;

class Selection extends \Netbaseteam\Marketplace\Block\Catalog\Product\Widget
{
    /**
     * @var string
     */
    protected $_template = 'Netbaseteam_Marketplace::catalog/product/edit/bundle/selection.phtml';

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Bundle\Model\Source\Option\Selection\Price\Type
     */
    protected $_priceType;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesno;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\Code\NameBuilder $nameBuilder
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param \Magento\Bundle\Model\Source\Option\Selection\Price\Type $priceType
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Math\Random $mathRandom,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Framework\Code\NameBuilder $nameBuilder,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Bundle\Model\Source\Option\Selection\Price\Type $priceType,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_catalogData = $catalogData;
        $this->_coreRegistry = $registry;
        $this->_priceType = $priceType;
        $this->_yesno = $yesno;
        parent::__construct($context, $mathRandom, $formKey, $nameBuilder, $data);
    }

    /**
     * Initialize bundle option selection block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }

    /**
     * Return field id
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'bundle_selection';
    }

    /**
     * Return field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'bundle_selections';
    }

    /**
     * Prepare block layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'selection_delete_button',
            'Netbaseteam\Marketplace\Block\Catalog\Product\Widget\Button',
            ['label' => __('Delete'), 'class' => 'action-delete', 'on_click' => 'bSelection.remove(event)']
        );
        return parent::_prepareLayout();
    }

    /**
     * Retrieve delete button html
     *
     * @return string
     */
    public function getSelectionDeleteButtonHtml()
    {
        return $this->getChildHtml('selection_delete_button');
    }

    /**
     * Retrieve price type select html
     *
     * @return string
     */
    public function getPriceTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            [
                'id' => $this->getFieldId() . '_<%- data.index %>_price_type',
                'class' => 'select select-product-option-type required-option-select',
            ]
        )->setName(
            $this->getFieldName() . '[<%- data.parentIndex %>][<%- data.index %>][selection_price_type]'
        )->setOptions(
            $this->_priceType->toOptionArray()
        );
        if ($this->getCanEditPrice() === false) {
            $select->setExtraParams('disabled="disabled"');
        }
        return $select->getHtml();
    }

    /**
     * Retrieve qty type select html
     *
     * @return string
     */
    public function getQtyTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            ['id' => $this->getFieldId() . '_<%- data.index %>_can_change_qty', 'class' => 'select']
        )->setName(
            $this->getFieldName() . '[<%- data.parentIndex %>][<%- data.index %>][selection_can_change_qty]'
        )->setOptions(
            $this->_yesno->toOptionArray()
        );

        return $select->getHtml();
    }

    /**
     * Return search url
     *
     * @return string
     */
    public function getSelectionSearchUrl()
    {
        return $this->getUrl('marketplace/product_bundle/grid');
    }

    /**
     * Check if used website scope price
     *
     * @return string
     */
    public function isUsedWebsitePrice()
    {
        $product = $this->_coreRegistry->registry('product');
        return !$this->_catalogData->isPriceGlobal() && $product->getStoreId();
    }

    /**
     * Retrieve price scope checkbox html
     *
     * @return string
     */
    public function getCheckboxScopeHtml()
    {
        $checkboxHtml = '';
        if ($this->isUsedWebsitePrice()) {
            $fieldsId = $this->getFieldId() . '_<%- data.index %>_price_scope';
            $name = $this->getFieldName() . '[<%- data.parentIndex %>][<%- data.index %>][default_price_scope]';
            $class = 'bundle-option-price-scope-checkbox';
            $label = __('Use Default Value');
            $disabled = $this->getCanEditPrice() === false ? ' disabled="disabled"' : '';
            $checkboxHtml = '<input type="checkbox" id="' .
                $fieldsId .
                '" class="' .
                $class .
                '" name="' .
                $name .
                '"' .
                $disabled .
                ' value="1" />';
            $checkboxHtml .= '<label class="normal" for="' . $fieldsId . '">' . $label . '</label>';
        }
        return $checkboxHtml;
    }
}
