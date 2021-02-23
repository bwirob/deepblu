<?php
namespace Cmsmart\Brandcategory\Block\Adminhtml\Brandcategory;

/**
 * Adminhtml Brandcategory grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Cmsmart\Brandcategory\Model\Brandcategory
     */
    protected $_brandcategory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Cmsmart\Brandcategory\Model\Brandcategory $brandcategoryPage
     * @param \Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Cmsmart\Brandcategory\Model\Brandcategory $brandcategory,
        \Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_brandcategory = $brandcategory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('brandcategoryGrid');
        $this->setDefaultSort('brandcategory_id');
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
        /* @var $collection \Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory\Collection */
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
        $this->addColumn('brandcategory_id', [
            'header'    => __('ID'),
            'index'     => 'brandcategory_id',
			'width'     => '50px',
        ]);
		
		$this->addColumn(
			'logo', 
			[
				'header' => __('Logo'), 
				'index' => 'logo',
				'align'		=> 'center',
				'filter'	=> false,
				'renderer'  => '\Cmsmart\Brandcategory\Block\Adminhtml\Renderer\Logo',
			]
		);
        
        $this->addColumn('brand_name', 
			[
			'header' => __('Brand Name'), 
			'index' => 'brand_name',
			]
		);
		
        $this->addColumn('description', 
			[
			'header' => __('Description'), 
			'index' => 'description',
			'width'     => '650px',
			]
		);
		
		$this->addColumn('position', 
			[
			'header' => __('Position'), 
			'index' => 'position',
			]
		);
        
		$this->addColumn(
			'status', 
			[
				'header' => __('Status'), 
				'index' => 'status',
				'type' =>  'options',
				'width'     => '50px',
				'options' => $this->_brandcategory->getStatusArray(),
			]
		);
        
        $this->addColumn(
            'action',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'brandcategory_id'
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
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['brandcategory_id' => $row->getId()]);
    }

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
