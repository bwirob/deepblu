<?php
namespace Cmsmart\Megamenu\Block\Adminhtml\Pgrid;

class Vergrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	/**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;
  
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Backend\Helper\Data $backendHelper,
		\Magento\Store\Model\WebsiteFactory $websiteFactory,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Model\Product\Type $type,
		\Magento\Catalog\Model\Product\Attribute\Source\Status $status,
		\Magento\Catalog\Model\Product\Visibility $visibility,
		\Magento\Framework\Module\Manager $moduleManager,
		array $data = []
	) {
		$this->_websiteFactory = $websiteFactory;
		$this->_setsFactory = $setsFactory;
		$this->_productFactory = $productFactory;
		$this->_type = $type;
		$this->_status = $status;
		$this->_visibility = $visibility;
		$this->moduleManager = $moduleManager;
		parent::__construct($context, $backendHelper, $data);
	}

	public function _construct()
	{
	  parent::_construct();
	  $this->setId('vgridGrid');
	  $this->setDefaultSort('pgrid_id');
	  $this->setDefaultDir('ASC');
	  $this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
	}

	protected function _prepareCollection()
	{
	 
		$collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
			'sku'
		)->addAttributeToSelect(
			'name'
		);
		
		$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
		$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
			
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
	  $this->addColumn('choose_id', array(
			'header_css_class' => 'a-center',
			'header'    => __('Select'),
			'type'      => 'checkbox',
			'index' 	=> 'choose_id',
			'align'     => 'center',
			'width'     => '50px',
			'sortable'  => false,
			/* 'renderer'  => 'megamenu/adminhtml_renderer_checkbox', */
			'renderer'  => '\Cmsmart\Megamenu\Block\Adminhtml\Megamenu\Grid\Renderer\Vercheckbox',
			/* 'filter_condition_callback' => array($this, '_filterIDCondition'), */
			'filter_condition_callback' => array($this, '_filterIDCondition'),
	  ));
	  
	  $this->addColumn('entity_id', array(
		  'header'    => __('ID'),
		  'align'     =>'right',
		  'width'     => '50px',
		  'index'     => 'entity_id',
		  'type' => 'number',
	  ));

	  $this->addColumn('sku', array(
		  'header'    => __('Sku'),
		  'align'     =>'left',
		  'index'     => 'sku',
	  ));
	  
	  $this->addColumn('name', array(
		  'header'    => __('Name'),
		  'align'     =>'left',
		  'index'     => 'name',
	  ));

	  return parent::_prepareColumns();
	}

	public function getGridUrl()
	{
		return $this->getUrl('megamenu/pgrid/vergrid', ['_current' => false]);
	}

	protected function _filterIDCondition($collection, $column)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$catalogSession = $objectManager->create('Magento\Catalog\Model\Session');
	
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
		$pids = array();
		$pids = explode(",", $catalogSession->getVerSelectedProducts());
	   
		$this->getCollection()->addAttributeToFilter('entity_id', array('in' => $pids));;
	}

}