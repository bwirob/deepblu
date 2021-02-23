<?php

namespace Netbaseteam\Marketplace\Block\Seller\Profile;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Product list
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ListProduct extends AbstractProduct implements IdentityInterface
{
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
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

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
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Netbaseteam\Marketplace\Model\ProductFactory $marketplaceProductFactory,
        \Netbaseteam\Marketplace\Model\Sellerdata $sellerData,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\ResourceModel\Order\Item $item,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Netbaseteam\Marketplace\Model\VacationFactory $vacationFactory,
        \Netbaseteam\Marketplace\Helper\Data $mpHelper,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory,
        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $this->_marketplaceProductFactory = $marketplaceProductFactory;
        $this->_sellerData = $sellerData;
        $this->_productFactory = $productFactory;
        $this->_salesResource = $item;
        $this->_orderItemFactory = $orderItemFactory;
        $this->_vacationFactory = $vacationFactory;
        $this->_mpHelper = $mpHelper;
        $this->_attributeFactory = $attributeFactory;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->getProductCollection();
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
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return 'grid';
        //return $this->getChildBlock('toolbar')->getCurrentMode();
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
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        //$toolbar->setCollection($collection);

        //$this->setChild('toolbar', $toolbar);

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'mp.list.product.record.pager'
        )->setCollection($collection);
        $this->setChild('pager', $pager);

        $this->_eventManager->dispatch(
            'seller_block_product_list_collection',
            ['collection' => $this->_getProductCollection()]
        );

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
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

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
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

    /**
     * @return mixed
     */
    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return \Magento\Catalog\Model\Config
     */
    protected function _getConfig()
    {
        return $this->_catalogConfig;
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Block\Product\ListProduct
     */
    public function prepareSortableFieldsByCategory($category)
    {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            $categorySortBy = $this->getDefaultSortBy() ?: $category->getDefaultSortBy();
            if ($categorySortBy) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
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
        // $category = $this->getLayer()->getCurrentCategory();
        // if ($category) {
        //     $identities[] = Product::CACHE_PRODUCT_CATEGORY_TAG . '_' . $category->getId();
        // }
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

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }

    /**
     * @return \Magento\Framework\Pricing\Render
     */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default');
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getNewProductCollection()
    {
        $productCollection = $this->getProductCollection()->setOrder('entity_id','DESC');
        return $productCollection;
    }

    public function getHotProductCollection() 
    {
        $orderCollection = $this->_orderItemFactory->create()->getCollection()->setOrder('qty_ordered','DESC');
        $productIds = array();
        foreach ($orderCollection as $item) {
            $productIds[] = $item->getProductId();
        }

        $productCollection = $this->getProductCollection()->addFieldToFilter('entity_id',array('in'=>$productIds));

        return $productCollection;
    }

    public function getProductCollection() 
    {
        $categoryId = (int)$this->getRequest()->getParam('cat');
        $priceFilter = $this->getRequest()->getParam('price');
        $productName = $this->getRequest()->getParam('product_name');        
        
        $sellerID = $this->getSellerId();

        $mpCollection = $this->_marketplaceProductFactory
            ->create()->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('seller_id', $sellerID);

        $products=array();
        foreach($mpCollection as $data){
            array_push($products,$data->getProductId());
        }


        $collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id',array('in'=>$products))
            ->addAttributeToFilter('status', array('eq'=>'1'))
            ->addAttributeToFilter('visibility', array('neq' => '1'));

        if($productName) {
            $collection->addAttributeToFilter('name', array('like'=>'%'.$productName.'%'));
        }

        if ($categoryId) {
            $collection->addCategoriesFilter(['in' => $categoryId]);
        }
        
        if ($priceFilter) {
            $priceReq = explode("-",$priceFilter);
            $minPrice = $priceReq[0];
            $maxPrice = $priceReq[1];

            $collection->addAttributeToFilter('price', array(
                    array('from' => $minPrice, 'to' => $maxPrice),
                )
            );
        }
        return $collection;
    }

    public function getProductAttributes() {
        $attributeInfo = $this->_attributeFactory->getCollection();
        $attributeCodes = array();
        foreach($attributeInfo as $attributes)
        {
            $attributeCodes[] = $attributes->getAttributeCode();
        }

        return $attributeCodes;
    }

    public function getVacation() 
    {
        $isVacation = $this->_mpHelper->isVacation();

        if ($isVacation == 1) {
            $sellerID  = $this->getSellerId();

            $vaCollection = $this->_vacationFactory
                ->create()->getCollection()
                ->addFieldToFilter('vacation_status', 1)
                ->addFieldToFilter('seller_id', $sellerID);
            $vacationData = '';
            if(!empty($vaCollection->getData())) {
                $vacationData = $vaCollection->getData()[0];
            }

            if (!empty($vacationData)) {
                $dateNow = date_create(date('m/d/Y h:i:s a', time()));
                $dateFrom = date_create($vacationData['date_from']);
                $dateTo = date_create($vacationData['date_to']);

                $diff = (int)date_diff($dateFrom, $dateNow)->format("%R%h%i%s%a");
                if ($vacationData['date_to'] && $diff > 0) {
                    return $vacationData;
                }
                return null;
            }

            return null;
        }

        return null;
    }

    public function getSellerId() 
    {
        $shopID = $this->getRequest()->getParam('shop');

        $sellerDataCollection = $this->_sellerData->getCollection()
            ->addFieldToSelect('seller_id')
            ->addFieldToFilter('shop_id',$shopID);
        $sellerID = '';
        if ($sellerDataCollection->getAllIds()) {
            $sellerID = $sellerDataCollection->getData('seller_id');
        }

        return $sellerID;
    }
}
