<?php
namespace Netbaseteam\Shopbybrand\Block;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
class ProductBrand extends \Magento\Framework\View\Element\Template
{
	 protected $_shopbybrandCollection = null;
    
     private $product;

     protected $registry;
    /**
     * Shopbybrand factory
     *
     * @var \Netbaseteam\Shopbybrand\Model\ShopbybrandFactory
     */
    protected $_shopbybrandCollectionFactory;
    
    /** @var \Netbaseteam\Shopbybrand\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand\CollectionFactory $shopbybrandCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Registry $registry,
        \Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand\CollectionFactory $shopbybrandCollectionFactory,
        \Netbaseteam\Shopbybrand\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_shopbybrandCollectionFactory = $shopbybrandCollectionFactory;
        $this->registry = $registry;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    private function getProduct()
    {
        if (is_null($this->product)) {
            $this->product = $this->registry->registry('product');

            if (!$this->product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }
        }
        return $this->product;
    }
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }
    /**
     * Retrieve shopbybrand collection
     *
     * @return Cmsmart\Shopbybrand\Model\ResourceModel\Shopbybrand\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_shopbybrandCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared shopbybrand collection
     *
     * @return Cmsmart\Shopbybrand\Model\ResourceModel\Shopbybrand\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_shopbybrandCollection)) {
            $this->_shopbybrandCollection = $this->_getCollection();
            $this->_shopbybrandCollection->setCurPage($this->getCurrentPage());
            $this->_shopbybrandCollection->setPageSize($this->_dataHelper->getShopbybrandPerPage());
        }

        return $this->_shopbybrandCollection;
    }
    
    /**
     * Fetch the current page for the shopbybrand list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param Cmsmart\Shopbybrand\Model\Shopbybrand $shopbybrandItem
     * @return string
     */
    public function getItemUrl($shopbybrandItem)
    {
        return $this->getUrl('*/*/view', array('id' => $shopbybrandItem->getId()));
    }
    
    /**
     * Return URL for resized Shopbybrand Item image
     *
     * @param Cmsmart\Shopbybrand\Model\Shopbybrand $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('shopbybrand_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $shopbybrandPerPage = $this->_dataHelper->getShopbybrandPerPage();

            $pager->setAvailableLimit([$shopbybrandPerPage => $shopbybrandPerPage]);
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(TRUE);
            $pager->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            return $pager->toHtml();
        }

        return NULL;
    }
    public function getShow_Brand()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/Product_Page_Settings/Show_Brand',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getShow_Brand_Name()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/Product_Page_Settings/Show_Brand_Name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getShow_Product_Count()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/Product_Page_Settings/Show_Product_Count',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getLogo_Width()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/list_page_settings/Logo_Width',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getLogo_Height()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/list_page_settings/Logo_Height',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}