<?php
namespace Netbaseteam\Navigation\Plugin\Model\Layer;

class FilterByStock
{
    const CONFIG_ENABLED_XML_PATH = 'cmsmart_navigation/stock_filter/enable';
    const STOCK_FILTER_POSITION = 'cmsmart_navigation/stock_filter/position';
    const STOCK_FILTER_CLASS = 'Netbaseteam\Navigation\Model\Layer\Filter\Stock';
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;
    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_layer;
    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Status
     */
    protected $_stockResource;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected $_moduleHelper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Netbaseteam\Navigation\Helper\Data $moduleHelper
    )
    {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_stockResource = $stockResource;
        $this->_scopeConfig = $scopeConfig;
        $this->_moduleHelper = $moduleHelper;
    }


    /**
     * @return bool
     */
    public function isActived()
    {
        $outOfStockEnabled = $this->_scopeConfig->isSetFlag(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $extensionEnabled = $this->_scopeConfig->isSetFlag(
            self::CONFIG_ENABLED_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $outOfStockEnabled && $extensionEnabled;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\FilterList\Interceptor $filterList
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array
     */
    public function beforeGetFilters(
        \Magento\Catalog\Model\Layer\FilterList\Interceptor $filterList,
        \Magento\Catalog\Model\Layer $layer
    )
    {
        $this->_layer = $layer;
        if ($this->_moduleHelper->isEnabled() && $this->isActived()) {
            $collection = $layer->getProductCollection();
            $websiteId = $this->_storeManager->getStore($collection->getStoreId())->getWebsiteId();
            $this->_addStockStatusToSelect($collection->getSelect(), $websiteId);
        }
        return array($layer);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\FilterList\Interceptor $filterList
     * @param array $filters
     * @return array
     */
    public function afterGetFilters(
        \Magento\Catalog\Model\Layer\FilterList\Interceptor $filterList,
        array $filters
    )
    {
        if ($this->_moduleHelper->isEnabled() && $this->isActived()) {
            $stockFilter = $this->getStockFilter();
//            array_unshift($filters, $stockFilter);

            $filters = array_merge(
                array_slice(
                    $filters,
                    0,
                    $this->Position()
                ),
                array_slice(
                    $filters,
                    $this->Position(),
                    count($filters) - 1
                )
            );
        }
        return $filters;
    }

    public function Position() {
        return $this->_scopeConfig->getValue(
            self::STOCK_FILTER_POSITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getStockFilter()
    {
        $filter = $this->_objectManager->create(
            $this->getStockFilterClass(),
            ['layer' => $this->_layer]
        );
        return $filter;
    }

    /**
     * @return string
     */
    public function getStockFilterClass()
    {
        return self::STOCK_FILTER_CLASS;
    }

    /**
     * @param \Zend_Db_Select $select
     * @param $websiteId
     * @return $this
     */
    protected function _addStockStatusToSelect(\Zend_Db_Select $select, $websiteId)
    {
        $from = $select->getPart(\Zend_Db_Select::FROM);
        if (!isset($from['stock_status_index'])) {
            $select->joinLeft(
                ['stock_status' => $this->_stockResource->getMainTable()],
                'e.entity_id = stock_status.product_id AND stock_status.website_id=' . $websiteId,
                ['is_salable' => 'stock_status.stock_status']
            );
        }
        return $this;
    }
}
