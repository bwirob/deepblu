<?php
namespace Netbase\Product\Block\Adminhtml\Typevalue;

/**
 * Adminhtml Product grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Netbase\Product\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Netbase\Product\Model\Product
     */
    protected $_product;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Netbase\Product\Model\Product $productPage
     * @param \Netbase\Product\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Netbase\Product\Model\Product $product,
        \Netbase\Product\Model\ResourceModel\Typevalue\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_product = $product;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Netbase\Product\Model\ResourceModel\Product\Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        /* $this->addColumn('id', [
            'header'    => __('ID'),
            'index'     => 'id',
			'width'		=> '80px',
        ]); */
        
		$this->addColumn(
			'image', 
			[
				'header' => __('Image'), 
				'index' => 'image',
				'align' => 'center',
                'width'		=> '200px',
				'filter'	=> false,
				'renderer'  => '\Netbase\Product\Block\Adminhtml\Renderer\Image',
			]
		);
		
		
        $this->addColumn('title', ['header' => __('Title'), 'index' => 'title']);
        
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		$type = $objectManagerr->get('\Netbase\Product\Model\ResourceModel\Product\CollectionFactory');
		$mCollection = $type->create();
		
		$cate_arr = array();
		$cate_arr[""] = "-- Please select -- ";
		foreach ($mCollection as $cat):    
			$cate_arr[$cat->getId()] = $cat->getTitle();
		endforeach;
		
		$this->addColumn(
			'alias', 
			[
				'header' => __('Section Type'), 
				'index' => 'alias',
				'type' =>  'options',
				'width'     => '250px',
				'options' => $cate_arr,
			]
		);
		
        //$this->addColumn('alias', ['header' => __('Alias'), 'index' => 'alias']);

		$this->addColumn('content', ['header' => __('Content'), 'index' => 'content']);
		
        $this->addColumn(
            'action',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
				'align' => 'center',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    // public function getRowUrl($row)
    // {
        // return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    // }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
