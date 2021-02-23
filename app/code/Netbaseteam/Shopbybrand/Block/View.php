<?php

namespace Netbaseteam\Shopbybrand\Block;

class View extends \Magento\Framework\View\Element\Template
{
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
     public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__($this->geturlkey()));
        $this->pageConfig->setKeywords($this->get_meta_keys());
        $this->pageConfig->setDescription($this->get_meta_description());
        parent::_prepareLayout();
    /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
           'Magento\Theme\Block\Html\Pager',
           'brand.view.pager'
        );
        $pager->setLimit(12)
            ->setShowAmounts(false)
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        
        return $this;
    }
    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml();
    }
 
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
    public function get_meta_keys()
    {
        $id = $this->getRequest()->getParam('id');
        $_shopbybrand = $this->getCollection(); 
        foreach ($_shopbybrand as $shopbybrandItem){
            if ($id == $shopbybrandItem->getbrand_id()) {
                $meta_key = $shopbybrandItem->getMeta_keyworlds();# code...
            }
        }
        return $meta_key;
    }
    public function get_meta_description()
    {
        $id = $this->getRequest()->getParam('id');
        $_shopbybrand = $this->getCollection(); 
        foreach ($_shopbybrand as $shopbybrandItem){
            if ($id == $shopbybrandItem->getbrand_id()) {
                $meta_description = $shopbybrandItem->getMeta_description();# code...
            }
        }
        return $meta_description;
    }
    public function geturlkey()
    {  
        $id = $this->getRequest()->getParam('id');
        $_shopbybrand = $this->getCollection(); 
        foreach ($_shopbybrand as $shopbybrandItem){
            if ($id == $shopbybrandItem->getbrand_id()) {
                $urlkey = $shopbybrandItem->geturlkey();# code...
            }
        }
        return $urlkey;
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
