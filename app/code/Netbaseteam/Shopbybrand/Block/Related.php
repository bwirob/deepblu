<?php
namespace Netbaseteam\Shopbybrand\Block;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
class Related extends \Magento\Framework\View\Element\Template
{
    private $product;
    
	protected $_imageHelper;
    /**
     * Shopbybrand collection
     *
     * @var Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand\Collection
     */
    protected $_shopbybrandCollection = null;
    
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
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\View\Element\Template\Context $httpcontext,
        \Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand\CollectionFactory $shopbybrandCollectionFactory,
        \Netbaseteam\Shopbybrand\Helper\Data $dataHelper,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        array $data = []
    ) {
        $this->_imageHelper = $context->getImageHelper();
        $this->_cartHelper = $context->getCartHelper();
        $this->_productloader = $_productloader;
        $this->_shopbybrandCollectionFactory = $shopbybrandCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve shopbybrand collection
     *
     * @return Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand\Collection
     */
    
    public function getAddToCartUrl($product, $additional = [])
    {
            return $this->_cartHelper->getAddUrl($product, $additional);
    }

    protected function _getCollection()
    {
        $collection = $this->_shopbybrandCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared shopbybrand collection
     *
     * @return Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand\Collection
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
    public function imageHelperObj(){
        return $this->_imageHelper;
    }

    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }

    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param Netbaseteam\Shopbybrand\Model\Shopbybrand $shopbybrandItem
     * @return string
     */
    public function getItemUrl($shopbybrandItem)
    {
        return $this->getUrl('*/*/view', array('id' => $shopbybrandItem->getId()));
    }
    
    /**
     * Return URL for resized Shopbybrand Item image
     *
     * @param Netbaseteam\Shopbybrand\Model\Shopbybrand $item
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
    public function getShow_Related_Products_By_Brand()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/Product_Page_Settings/Show_Related_Products_By_Brand',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNumber_Of_Related_Products_By_Brand_Will_Display()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/Product_Page_Settings/Number_Of_Related_Products_By_Brand_Will_Display',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}