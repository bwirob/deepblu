<?php

namespace Netbaseteam\Faq\Block\Adminhtml\Faq\Edit\Tab;

use Netbaseteam\Faq\Model\FaqFactory;

class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var   \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * Contact factory
     *
     * @var ContactFactory
     */

    protected $_faqFactory;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    protected $_objectManager = null;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param ContactFactory $attachmentFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        FaqFactory $faqFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Type $type,
        array $data = []
    ) {
        $this->_faqFactory = $faqFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        $this->_type = $type;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('faq_id')) {
            $this->setDefaultFilter(array('in_product' => 1));
        }
    }

    /**
     * add Column Filter To Collection
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_product') { 
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToFilter('status', array('eq'=>'1'))
            ->addAttributeToFilter('visibility', array('neq' => '1'));
           
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $model = $this->_objectManager->get('\Netbaseteam\Faq\Model\Faq');

        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '100px',
            ]
        );
        
        $optionType = $this->_type->getOptionArray();
        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'index' => 'type_id',
                'type' => 'options',
                'options' => $optionType
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '50px',
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsgrid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return '';
    }

    protected function _getSelectedProducts()
    {
        $faq = $this->getFaq();
        if($faq->getProductIds()){
            $productIds = explode('&', $faq->getProductIds());
        }else{
            $productIds = [];
        }
        return  $productIds;
    }

    public function getSelectedProducts()
    {
        $faq = $this->getFaq();
        $productids = $faq->getProductIds();

        if ($productids) {
            $selected = explode('&', $faq->getProductIds());
        }else{
            $selected=[];
        }
        return $selected;
    }

    protected function getFaq()
    {
        $faqId = $this->getRequest()->getParam('faq_id');       
        $faq   = $this->_faqFactory->create();
        if ($faqId) {
            $faq->load($faqId);
        }

        return $faq;
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return true;
    }
}
