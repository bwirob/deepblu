<?php

namespace Netbaseteam\Marketplace\Block\Adminhtml\Widget\Seller;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Chooser extends Extended
{
    /**
     * @var array
     */
    protected $_selectedSellers = [];


    /**
     * @var \Netbaseteam\Marketplace\Model\ResourceModel\Seller
     */
    protected $_resourceSeller;

    /**
     * @var \Netbaseteam\Marketplace\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $_collectionFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Netbaseteam\Marketplace\Model\ResourceModel\Seller\CollectionFactory $collectionFactory
     * @param \Netbaseteam\Marketplace\Model\ResourceModel\Seller $resourceSeller
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Netbaseteam\Marketplace\Model\ResourceModel\Sellerdata\CollectionFactory $collectionFactory,
        \Netbaseteam\Marketplace\Model\ResourceModel\Sellerdata $resourceSeller,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_resourceSeller = $resourceSeller;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Block construction, prepare grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('name');
        $this->setUseAjax(true);
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        if ($this->getUseMassaction()) {
            return "function (grid, element) {
                $(grid.containerId).fire('sellerNode:changed', {element: element});
            }";
        }
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        if (!$this->getUseMassaction()) {
            $chooserJsObject = $this->getId();
            return '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var productId = trElement.down("td").innerHTML;
                    var productName = trElement.down("td").next().next().innerHTML;
                    var optionLabel = productName;
                    var optionValue = "product/" + productId.replace(/^\s+|\s+$/g,"");
                    if (grid.categoryId) {
                        optionValue += "/" + grid.categoryId;
                    }
                    if (grid.categoryName) {
                        optionLabel = grid.categoryName + " / " + optionLabel;
                    }
                    ' .
                $chooserJsObject .
                '.setElementValue(optionValue);
                    ' .
                $chooserJsObject .
                '.setElementLabel(optionLabel);
                    ' .
                $chooserJsObject .
                '.close();
                }
            ';
        }
    }


    /**
     * Filter checked/unchecked rows in grid
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_sellers') {
            $selected = $this->getSelectedSellers();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('id', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare sellers collection, defined collection filters (category, product type)
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        /* @var $collection \Netbaseteam\Marketplace\Model\ResourceModel\Seller\Collection */
        $collection = $this->_collectionFactory->create();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for sellers grid
     *
     * @return Extended
     */
    protected function _prepareColumns()
    {
        if ($this->getUseMassaction()) {
            $this->addColumn(
                'in_sellers',
                [
                    'header_css_class' => 'a-center',
                    'type' => 'checkbox',
                    'name' => 'in_sellers',
                    'inline_css' => 'checkbox entities',
                    'field_name' => 'in_sellers',
                    'values' => $this->getSelectedSellers(),
                    'align' => 'center',
                    'index' => 'id',
                    'use_index' => true
                ]
            );
        }

        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        
        $this->addColumn(
            'chooser_name',
            [
                'header' => __('Seller'),
                'name' => 'chooser_name',
                'index' => 'shop_title',
                'header_css_class' => 'col-seller',
                'column_css_class' => 'col-seller'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Adds additional parameter to URL for loading only sellers grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'marketplace/widget/sellers',
            [
                'sellers_grid' => true,
                '_current' => true,
                'uniq_id' => $this->getId(),
                'use_massaction' => $this->getUseMassaction()
            ]
        );
    }

    /**
     * Setter
     *
     * @param array $selectedSellers
     * @return $this
     */
    public function setSelectedSellers($selectedSellers)
    {
        $this->_selectedSellers = $selectedSellers;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedSellers()
    {
        if ($selectedSellers = $this->getRequest()->getParam('selected_sellers', null)) {
            $this->setSelectedSellers($selectedSellers);
        }
        return $this->_selectedSellers;
    }
}
