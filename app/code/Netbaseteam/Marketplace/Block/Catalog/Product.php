<?php

/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Catalog;

class Product extends \Magento\Framework\View\Element\Template
{
    // protected $_mpColletion;
    // protected $_productCollection;
    
    /**
     * @var \Netbaseteam\Marketplace\Model\ProductFactory
     */
    protected $_marketplaceProductFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory $productFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Helper\Image $imageHelper
     */
    protected $imageHelper;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var \Magento\Catalog\Model\Product\TypeFactory
     */
    protected $_typeFactory;


    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Model\Product $productCollection
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Netbaseteam\Marketplace\Model\ProductFactory $marketplaceProductFactory,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $contextInterface,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Stdlib\DateTime\DateTime $formatDate,
        \Magento\Catalog\Model\Product\TypeFactory $typeFactory,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        \Magento\CatalogInventory\Api\StockStateInterface $stockItem,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        array $data = []
    )
    {
        $this->_marketplaceProductFactory = $marketplaceProductFactory;
        $this->_sellerdataFactory = $sellerdataFactory;
        $this->_productFactory = $productFactory;
        $this->logger = $context->getLogger();
        $this->imageHelper = $context->getImageHelper();
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $context->getStoreManager();
        $this->context = $contextInterface;
        $this->_registry = $context->getRegistry();
        $this->formkey = $formKey;
        $this->_formatDate = $formatDate;
        $this->_typeFactory = $typeFactory;
        $this->_helper = $helper;
        $this->_stockItem = $stockItem;
        $this->_orderItemFactory = $orderItemFactory;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    /**
     * function to get rewards point transaction of customer
     *
     * @return reward product collection
     */
    public function getProductCollection()
    {
        $mpCollection = $this->getMpCollection();
        $products = array();
        foreach ($mpCollection as $data) {
            array_push($products, $data->getProductId());
        }

        $collection = $this->getProducts();
        $collection->addFieldToFilter('entity_id', array('in' => $products));
            
        return $collection;
    }

    public function getProductInfomation($productId)
    {
        $product = $this->_productFactory->create()->load($productId);
        return $product;
    }

    public function getSpecialProduct()
    {
        $collection = $this->getProducts();

        $productIds=array();
        foreach($collection as $data){
            if($data->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $product = $data->getTypeInstance()->getUsedProducts($data);
                foreach ($product as $item) {
                    array_push($productIds,$item->getEntityId());
                }
            }
        }
        return $productIds;
    }

    public function getQtyConfirmed($productId) {
        $product = $this->getProductInfomation($productId);
        return $this->_stockItem->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
    }

    public function getQtySold($productId) {
        $orderCollection = $this->_orderItemFactory->create()->getCollection()
            ->addFieldToFilter('product_id', $productId);
        $qtySold = 0;
        foreach ($orderCollection as $item) {
            $qtySold = $item->getQtyOrdered() - $item->getQtyCanceled();
        }

        return $qtySold;
    }

    public function getQtyPending($productId) {
        $product = $this->getProductInfomation($productId);
        return $product->getData('quantity_and_stock_status')['qty'];
    }

    public function getThumbnail($productId)
    {
        $product = $this->getProductInfomation($productId);
        $imageHelper = $this->imageHelper->init($product, 'product_page_image_small')->resize(55);
        $src = $imageHelper->getUrl();
        $alt = $this->getAlt($product) ?: $imageHelper->getLabel();

        $imageHtml = "<img alt='$alt' src='$src' class='admin__control-thumbnail' />";
        return $imageHtml;
    }

    public function getFormKey()
    {
        return $this->formkey->getFormKey();
    }

    public function formatStatus($value)
    {
        $status = '';
        if ($value == 0) {
            $status = "Pending";
        } elseif ($value == 1) {
            $status = "Approved";
        } elseif ($value == 2) {
            $status = "Disapproved";
        }
        return $status;
    }

    public function getAllowedProductTypes()
    {
        $allowed = explode(',', $this->_helper->getAllowedProductTypes());
        $types = $this->_typeFactory->create()->getTypes();
        $this->_options = array();
        foreach ($types as $d) {
            if (in_array($d['name'], $allowed)) {
                $this->_options[] = ['label' => $d['label'], 'value' => $d['name']];
            }
        }
        return $this->_options;
    }

    public function getAllowedSets(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $coll = $objectManager->create(\Magento\Catalog\Model\Product\AttributeSet\Options::class);

        $allowed=explode(',',$this->_helper->getAllowedAttributesetIds());
        $options = array();
        foreach($coll->toOptionArray() as $d){
            if(in_array($d['value'], $allowed)) {
                $options[] = ['label' => $d['label'], 'value' => $d['value']];
            }
        }
        return $options;
    }

    public function getMpCollection()
    {
        $sellerId = $this->_helper->getSellerId();
        $mpCollection = $this->_marketplaceProductFactory
            ->create()->getCollection()
            ->addFieldToFilter('seller_id', $sellerId);
        return $mpCollection;
    }

    public function getMpId($productId)
    {
        $mpId = '';
        $sellerId = $this->_helper->getSellerId();

        $collection = $this->_marketplaceProductFactory
            ->create()->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('seller_id', $sellerId);
        if ($collection->getData()) {
            foreach ($collection as $item) {
                $mpId = $item->getId();
            }
        }
        return $mpId;
    }

    public function getProducts()
    {
        $product = $this->_productFactory->create()->getCollection()
            ->addAttributeToSelect('*');

        return $product;
    }

    public function getSellerData () {
        $sellerId = $this->_helper->getSellerId();

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
}
