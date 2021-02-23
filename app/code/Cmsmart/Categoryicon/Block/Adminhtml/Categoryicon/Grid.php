<?php
namespace Cmsmart\Categoryicon\Block\Adminhtml\Categoryicon;

/**
 * Adminhtml Categoryicon grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Cmsmart\Categoryicon\Model\Categoryicon
     */
    protected $_categoryicon;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Cmsmart\Categoryicon\Model\Categoryicon $categoryiconPage
     * @param \Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Cmsmart\Categoryicon\Model\Categoryicon $categoryicon,
        \Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_categoryicon = $categoryicon;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
		$this->setTemplate('Cmsmart_Categoryicon::categories.phtml');
        /* $this->setId('categoryiconGrid');
        $this->setDefaultSort('categoryicon_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true); */
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection1()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns1()
    {
        $this->addColumn('categoryicon_id', [
            'header'    => __('ID'),
            'index'     => 'categoryicon_id',
        ]);
        
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		$categoryFactory = $objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
		$helper = $objectManagerr->get('\Cmsmart\Categoryicon\Helper\Data');
		
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
        
        $this->addColumn(
			'icon_init', 
			[
				'header' => __('Category Icon'), 
				'index' => 'icon_init',
				'width'     => '250px',
				'filter'	=> false,
				'renderer'  => '\Cmsmart\Categoryicon\Block\Adminhtml\Renderer\Iconinit',
			]
		);
		
		$this->addColumn(
			'icon_hover', 
			[
				'header' => __('Category Icon Hover'), 
				'index' => 'icon_hover',
				'width'     => '250px',
				'filter'	=> false,
				'renderer'  => '\Cmsmart\Categoryicon\Block\Adminhtml\Renderer\Iconhover',
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
                        'field' => 'categoryicon_id'
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
        return $this->getUrl('*/*/edit', ['categoryicon_id' => $row->getId()]);
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
