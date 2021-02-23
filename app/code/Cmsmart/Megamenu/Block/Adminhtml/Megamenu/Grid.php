<?php
namespace Cmsmart\Megamenu\Block\Adminhtml\Megamenu;

/**
 * Adminhtml Megamenu grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Cmsmart\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Cmsmart\Megamenu\Model\Megamenu
     */
    protected $_megamenu;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Cmsmart\Megamenu\Model\Megamenu $megamenuPage
     * @param \Cmsmart\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Cmsmart\Megamenu\Model\Megamenu $megamenu,
        \Cmsmart\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_megamenu = $megamenu;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('megamenuGrid');
        $this->setDefaultSort('megamenu_id');
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
        /* @var $collection \Cmsmart\Megamenu\Model\ResourceModel\Megamenu\Collection */
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
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		
        $this->addColumn('megamenu_id', [
            'header'    => __('Menu ID'),
            'index'     => 'megamenu_id',
			'type' => 'number',
			'width'     => '50px',
			'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);
        
        $this->addColumn(
			'category_id', 
			[
				'header' => __('Catgory ID'), 
				'index' => 'category_id',
				'type' => 'number',
				'width'     => '50px',
				'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
				/* 'renderer'  => '\Cmsmart\Megamenu\Block\Adminhtml\Megamenu\Grid\Renderer\Category', */
			]
		);

		$categoryFactory = $objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
		$helper = $objectManagerr->get('\Cmsmart\Megamenu\Helper\Data');
		
		$categories = $categoryFactory->create()                              
					->addAttributeToSelect('*')
					->addFieldToFilter("parent_id", $helper->getRootCatID())
					->addFieldToFilter("is_active", 1)
					->addFieldToFilter("include_in_menu", 1)
					->setOrder('position', 'ASC');
		
		$cate_arr = array();
		$cate_arr[""] = "-- Please select -- ";
		foreach ($categories as $cat):    
			$cate_arr[$cat->getId()] = $cat->getName();
		endforeach;
		
		$this->addColumn(
			'category_name', 
			[
				'header' => __('Category Name'), 
				'index' => 'category_id',
				'type' =>  'options',
				'width'     => '250px',
				'options' => $cate_arr,
			]
		);
		
		$positionFactory = $objectManagerr->create('\Cmsmart\Megamenu\Model\Position');
		$this->addColumn(
			'position', 
			[
				'header' => __('Cat Position'), 
				'index' => 'position',
				'type' =>  'options',
				'options' => $positionFactory->getOptionArray(),
			]
		);
		
		/* $labelFactory = $objectManagerr->create('\Cmsmart\Megamenu\Model\Label');
		$this->addColumn(
			'top_label', 
			[
				'header' => __('Cat Label'), 
				'index' => 'top_label',
				'type' =>  'options',
				'options' => $labelFactory->getOptionArray(),
			]
		); */
		
		$this->addColumn(
			'mytype', 
			[
				'header' => __('Menu Type'), 
				'index' => 'mytype',
				'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
				'filter'	=> false,
				'renderer'  => '\Cmsmart\Megamenu\Block\Adminhtml\Megamenu\Grid\Renderer\Menutype',
			]
		);
		
        /* $this->addColumn('author', ['header' => __('Author'), 'index' => 'author']);
        
        $this->addColumn(
            'published_at',
            [
                'header' => __('Published On'),
                'index' => 'published_at',
                'type' => 'date',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );
        
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created'),
                'index' => 'created_at',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        ); */
        
        $this->addColumn(
            'action',
            [
                'header' => __('Detail'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('View'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'megamenu_id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
				'align'  => 'center',
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
        return $this->getUrl('*/*/edit', ['megamenu_id' => $row->getId()]);
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
