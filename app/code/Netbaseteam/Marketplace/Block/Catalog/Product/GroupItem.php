<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Catalog\Product;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

class GroupItem extends AbstractProduct implements IdentityInterface
{
    protected $_priceCurrency;

        /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';

        /**
     * Product Collection
     *
     * @var AbstractCollection
     */
    protected $_productCollection;

        /**
     * @param Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Product $product,
        \Netbaseteam\Marketplace\Model\ProductFactory $productFactory,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Review\Model\Rating $rating,
        array $data = []
    ) {
        $this->_product = $product;
        $this->_productFactory = $productFactory;
        $this->_sellerdataFactory = $sellerdataFactory;
        $this->urlHelper = $urlHelper;
        $this->_rating = $rating;
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    public function getCurrentProduct() {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'mp.list.same.product.record.pager'
        )->setCollection($collection);
        $this->setChild('pager', $pager);

        $this->_eventManager->dispatch(
            'seller_block_same_product',
            ['collection' => $this->_getProductCollection()]
        );

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }

        /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->getSameProduct();
        }

        return $this->_productCollection;
    }

        /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

        /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        return $block;
    }


    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

        /**
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

        /**
     * @param array|string|integer|\Magento\Framework\App\Config\Element $code
     * @return $this
     */
    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    public function getSameProduct() {
        $productName = $this->getCurrentProduct()->getName();
        $sameProducts = $this->_product->getCollection()
        ->addAttributeToSelect('*')
        ->addFieldToFilter('name', $productName);
        
        return $sameProducts;
    }

        /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->_getProductCollection() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        return $identities;
    }


    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    public function getSellerData ($pId = null) {
        if ($pId) {
            $productId = $pId;
        } else {
            $productId = $this->getCurrentProduct()->getId();            
        }

        $mpProduct = $this->_productFactory->create()->getCollection()->addFieldToFilter('product_id', $productId);
        $sellerId = '';
        
        if (count($mpProduct)) {
            $sellerId = $mpProduct->getData()[0]['seller_id'];
        }

        $sellerDataCollection = $this->_sellerdataFactory->create()->getCollection()
            ->addFieldToFilter('seller_id',$sellerId);

        $seller=array();
        foreach($sellerDataCollection as $data){
            array_push($seller,$data->getData());
        }
        if ($seller) {
            return $seller[0];
        } else {
            return null;
        }
    }

    public function getRatingAverage($sId = null)
    { 
        $rateSummary = 0;
        $rateCount = 0;

        $sellerId = '';
        if ($sId) {
            $sellerId = $sId;
        }

        $productIds = array();

        $sellerProduct = $this->_productFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);

        foreach ($sellerProduct as $item) {
            $productIds[]  = $item->getProductId();
        }

        if(count($productIds)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeId = $this->_storeManager->getStore()->getId();
            foreach($productIds as $productId) {
                $_product=$objectManager->create("Magento\Catalog\Model\Product")->load($productId);
                $_review=$objectManager->create("Magento\Review\Model\Review");
                $_reviewFact=$objectManager->get("Magento\Review\Model\ReviewFactory");
                $_reviewFact->create()->getEntitySummary($_product, $storeId);
                if($_product->getRatingSummary()->getRatingSummary()) {
                    $rateSummary += $_product->getRatingSummary()->getRatingSummary();
                    $rateCount++;
                }  
            }
        }  

        if ($rateCount) {
            return $rateSummary/$rateCount;
        }

        return 0;
    }

    public function getCountRating($sId = null)
    { 
        $ratings = 0;
        $rateCount = 0;

        $sellerId = '';
        if ($sId) {
            $sellerId = $sId;
        }

        $productIds = array();

        $sellerProduct = $this->_productFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);

        foreach ($sellerProduct as $item) {
            $productIds[]  = $item->getProductId();
        }

        if(count($productIds)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeId = $this->_storeManager->getStore()->getId();
            foreach($productIds as $productId) {
                $_product=$objectManager->create("Magento\Catalog\Model\Product")->load($productId);
                $_review=$objectManager->create("Magento\Review\Model\Review");
                $_reviewFact=$objectManager->get("Magento\Review\Model\ReviewFactory");
                $_reviewFact->create()->getEntitySummary($_product, $storeId);  
                $rateCount += $_product->getRatingSummary()->getReviewsCount();
            }
        }
        
        return $rateCount;
    }

}
