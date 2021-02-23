<?php

namespace Netbase\Product\Block;

/**
 * Product content block
 */
class Product extends \Magento\Framework\View\Element\Template
{
    /**
     * Product collection
     *
     * @var Netbase\Product\Model\ResourceModel\Product\Collection
     */
    protected $_productCollection = null;
    
    /**
     * Product factory
     *
     * @var \Netbase\Product\Model\ProductFactory
     */
    protected $_productCollectionFactory;
    
    /** @var \Netbase\Product\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Netbase\Product\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbase\Product\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Netbase\Product\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve product collection
     *
     * @return Netbase\Product\Model\ResourceModel\Product\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared product collection
     *
     * @return Netbase\Product\Model\ResourceModel\Product\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = $this->_getCollection();
            $this->_productCollection->setCurPage($this->getCurrentPage());
            $this->_productCollection->setPageSize($this->_dataHelper->getProductPerPage());
            $this->_productCollection->setOrder('published_at','asc');
        }

        return $this->_productCollection;
    }
    
    /**
     * Fetch the current page for the product list
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
     * @param Netbase\Product\Model\Product $productItem
     * @return string
     */
    public function getItemUrl($productItem)
    {
        return $this->getUrl('*/*/view', array('id' => $productItem->getId()));
    }
    
    /**
     * Return URL for resized Product Item image
     *
     * @param Netbase\Product\Model\Product $item
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
        $pager = $this->getChildBlock('product_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $productPerPage = $this->_dataHelper->getProductPerPage();

            $pager->setAvailableLimit([$productPerPage => $productPerPage]);
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
}
