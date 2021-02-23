<?php
namespace Netbaseteam\Productvideo\Block\Adminhtml\Pgrid;


class Relate extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Netbaseteam\Productvideo\Model\ResourceModel\Productvideo\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Netbaseteam\Productvideo\Model\Productvideo
     */
    protected $_productvideo;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Netbaseteam\Productvideo\Model\Productvideo $productvideoPage
     * @param \Netbaseteam\Productvideo\Model\ResourceModel\Productvideo\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Netbaseteam\Productvideo\Model\Productvideo $productvideo,
        \Netbaseteam\Productvideo\Model\ResourceModel\Productvideo\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_productvideo = $productvideo;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('relateGrid');
        $this->setDefaultSort('productvideo_id');
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
		$retCollection = $collection->addFieldToFilter("status", \Netbaseteam\Productvideo\Model\Source\Status::active);
        /* @var $collection \Netbaseteam\Productvideo\Model\ResourceModel\Productvideo\Collection */
        $this->setCollection($retCollection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
		$this->addColumn('relate_id', array(
			'header_css_class' => 'a-center',
			'header'    => __('Select'),
			'type'      => 'checkbox',
			'index' 	=> 'relate_id',
			'align'     => 'center',
			'width'     => '50px',
			'sortable'  => false,
			'renderer'  => '\Netbaseteam\Productvideo\Block\Adminhtml\Renderer\Checkrelate',
			'filter_condition_callback' => array($this, '_filterIDCondition1'),
		));
	  
        /* $this->addColumn('productvideo_id', [
            'header'    => __('ID'),
            'index'     => 'productvideo_id',
        ]); */
        
        $this->addColumn('title', ['header' => __('Title'), 'index' => 'title']);
        /* $this->addColumn('author', ['header' => __('Author'), 'index' => 'author']); */
		
        return parent::_prepareColumns();
    }

    public function getGridUrl()
	{
		return $this->getUrl('productvideo/pgrid/relate', ['_current' => false]);
	}
	
	protected function _filterIDCondition1($collection, $column)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$catalogSession = $objectManager->create('Magento\Catalog\Model\Session');
	
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
		$vids = array();
		$vids = explode(",", $catalogSession->getVrelate());
		$this->getCollection()->addFieldToFilter('productvideo_id', array('in' => $vids));
	}
}