<?php
namespace Netbaseteam\Shopbybrand\Block;
use Magento\Framework\View\Result\PageFactory;
class Index extends \Magento\Framework\View\Element\Template
{
    protected $resultPageFactory;
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
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand\CollectionFactory $shopbybrandCollectionFactory,
        \Netbaseteam\Shopbybrand\Helper\Data $dataHelper,
         PageFactory $resultPageFactory,
        array $data = []
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_shopbybrandCollectionFactory = $shopbybrandCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__($this->getPage_Title()));
        parent::_prepareLayout();
    /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
           'Magento\Theme\Block\Html\Pager',
           'brand.view.pager'
        );
        $pager->setLimit(99999)
            ->setShowAmounts(false)
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
 
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
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
    public function getPage_Title()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/list_page_settings/Page_Title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getShow_Brand_Name()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/list_page_settings/Show_Brand_Name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getShow_Featured_Brands()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/list_page_settings/Show_Featured_Brands',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
  
    public function getShow_Product_Count()
    {
        return $this->_scopeConfig->getValue(
            'shopbybrand/list_page_settings/Show_Product_Count',
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
    
