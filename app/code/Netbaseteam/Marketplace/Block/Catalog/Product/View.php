<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\AbstractProduct;

/**
 * Product View block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends AbstractProduct implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Netbaseteam\Marketplace\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductRepositoryInterface|\Magento\Framework\Pricing\PriceCurrencyInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Product $product,
        \Netbaseteam\Marketplace\Model\VacationFactory $vacationFactory,
        \Netbaseteam\Marketplace\Model\ProductFactory $marketplaceProductFactory,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Netbaseteam\Marketplace\Helper\Data $mpHelper,
        \Magento\Review\Model\Rating $rating,
        \Netbaseteam\Marketplace\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_productHelper = $productHelper;
        $this->urlEncoder = $urlEncoder;
        $this->_jsonEncoder = $jsonEncoder;
        $this->productTypeConfig = $productTypeConfig;
        $this->string = $string;
        $this->_localeFormat = $localeFormat;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->priceCurrency = $priceCurrency;
        $this->_product = $product;
        $this->_vacationFactory = $vacationFactory;
        $this->_marketplaceProductFactory = $marketplaceProductFactory;
        $this->_sellerdataFactory = $sellerdataFactory;
        $this->_mpHelper = $mpHelper;
        $this->_rating = $rating;
        $this->_productFactory = $productFactory;
        parent::__construct(
            $context,
            $data
        );
    }
    // @codingStandardsIgnoreEnd

    /**
     * Return wishlist widget options
     *
     * @return array
     * @deprecated
     */
    public function getWishlistOptions()
    {
        return ['productType' => $this->getProduct()->getTypeId()];
    }

    /**
     * Add meta information from product to head block
     *
     * @return \Magento\Catalog\Block\Product\View
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('Magento\Catalog\Block\Breadcrumbs');
        $product = $this->getProduct();
        if (!$product) {
            return parent::_prepareLayout();
        }

        $title = $product->getMetaTitle();
        if ($title) {
            $this->pageConfig->getTitle()->set($title);
        }
        $keyword = $product->getMetaKeyword();
        $currentCategory = $this->_coreRegistry->registry('current_category');
        if ($keyword) {
            $this->pageConfig->setKeywords($keyword);
        } elseif ($currentCategory) {
            $this->pageConfig->setKeywords($product->getName());
        }
        $description = $product->getMetaDescription();
        if ($description) {
            $this->pageConfig->setDescription($description);
        } else {
            $this->pageConfig->setDescription($this->string->substr($product->getDescription(), 0, 255));
        }
        if ($this->_productHelper->canUseCanonicalTag()) {
            $this->pageConfig->addRemotePageAsset(
                $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($product->getName());
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->_coreRegistry->registry('product') && $this->getProductId()) {
            $product = $this->productRepository->getById($this->getProductId());
            $this->_coreRegistry->register('product', $product);
        }
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend()
    {
        return false;
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')) {
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED;
        $addUrlValue = $this->_urlBuilder->getUrl('*/*/*', ['_use_rewrite' => true, '_current' => true]);
        $additional[$addUrlKey] = $this->urlEncoder->encode($addUrlValue);

        return $this->_cartHelper->getAddUrl($product, $additional);
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig()
    {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->getProduct();

        if (!$this->hasOptions()) {
            $config = [
                'productId' => $product->getId(),
                'priceFormat' => $this->_localeFormat->getPriceFormat()
            ];
            return $this->_jsonEncoder->encode($config);
        }

        $tierPrices = [];
        $tierPricesList = $product->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPrices[] = $this->priceCurrency->convert($tierPrice['price']->getValue());
        }
        $config = [
            'productId' => $product->getId(),
            'priceFormat' => $this->_localeFormat->getPriceFormat(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->priceCurrency->convert(
                        $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue()
                    ),
                    'adjustments' => []
                ],
                'basePrice' => [
                    'amount' => $this->priceCurrency->convert(
                        $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount()
                    ),
                    'adjustments' => []
                ],
                'finalPrice' => [
                    'amount' => $this->priceCurrency->convert(
                        $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue()
                    ),
                    'adjustments' => []
                ]
            ],
            'idSuffix' => '_clone',
            'tierPrices' => $tierPrices
        ];

        $responseObject = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject]);
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return $this->_jsonEncoder->encode($config);
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getTypeInstance()->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }

    /**
     * Check if product has required options
     *
     * @return bool
     */
    public function hasRequiredOptions()
    {
        return $this->getProduct()->getTypeInstance()->hasRequiredOptions($this->getProduct());
    }

    /**
     * Define if setting of product options must be shown instantly.
     * Used in case when options are usually hidden and shown only when user
     * presses some button or link. In editing mode we better show these options
     * instantly.
     *
     * @return bool
     */
    public function isStartCustomization()
    {
        return $this->getProduct()->getConfigureMode() || $this->_request->getParam('startcustomization');
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param null|\Magento\Catalog\Model\Product $product
     * @return int|float
     */
    public function getProductDefaultQty($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }

        return $qty;
    }

    /**
     * Get container name, where product options should be displayed
     *
     * @return string
     */
    public function getOptionsContainer()
    {
        return $this->getProduct()->getOptionsContainer() == 'container1' ? 'container1' : 'container2';
    }

    /**
     * Check whether quantity field should be rendered
     *
     * @return bool
     */
    public function shouldRenderQuantity()
    {
        return !$this->productTypeConfig->isProductSet($this->getProduct()->getTypeId());
    }

    /**
     * Get Validation Rules for Quantity field
     *
     * @return array
     */
    public function getQuantityValidators()
    {
        $validators = [];
        $validators['required-number'] = true;
        return $validators;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = $this->getProduct()->getIdentities();
        $category = $this->_coreRegistry->registry('current_category');
        if ($category) {
            $identities[] = Category::CACHE_TAG . '_' . $category->getId();
        }
        return $identities;
    }

    /**
     * Retrieve customer data object
     *
     * @return int
     */
    protected function getCustomerId()
    {
        return $this->getCustomer()->getId();
    }

    public function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    public function getVacation() {
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

    public function getVacationProductList($productId) {
        $isVacation = $this->_mpHelper->isVacation();

        if ($isVacation == 1) {
            $sellerData = $this->_productFactory->create()->getCollection()->addFieldToFilter('product_id', $productId)->addFieldToSelect('seller_id')->getData();

            if(count($sellerData)) {
                foreach ($sellerData as $sellerId) {
                    $vaCollection = $this->_vacationFactory
                        ->create()->getCollection()
                        ->addFieldToFilter('vacation_status', 1)
                        ->addFieldToFilter('seller_id', $sellerId['seller_id']);

                    $vacationData = '';
                    if (!empty($vaCollection->getData())) {
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
                }
            }

            return null;
        }

        return null;
    }

    public function getSellerId () {
        $productId = '';
        if(!empty($this->getProduct())) {
            $productId = $this->getProduct()->getId();
        }
        $sellerId = '';
        if ($productId) {
            $mpCollection = $this->_marketplaceProductFactory
                ->create()->getCollection()
                ->addFieldToFilter('product_id', $productId);
            foreach ($mpCollection as $item) {
                $sellerId = $item->getSellerId();
            }
        }

        return $sellerId;
    }

    public function getCountProduct() {
        $sellerId = $this->getSellerId();
        $mpCollection = $this->_marketplaceProductFactory
        ->create()->getCollection()
        ->addFieldToFilter('seller_id', $sellerId);

        return count($mpCollection);
    }

    public function getSellerData () {
        $sellerId = $this->getSellerId();

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

    public function getRatingAverage()
    { 
        $rateSummary = 0;
        $rateCount = 0;

        $sellerId = $this->getSellerId();
        $productIds = array();

        $sellerProduct = $this->_marketplaceProductFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);

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

    public function getRatingCount()
    { 
        $ratings = 0;
        $rateCount = 0;

        $sellerId = $this->getSellerId();
        $productIds = array();

        $sellerProduct = $this->_marketplaceProductFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);

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

    public function getSameProduct() {
        $productName = $this->getProduct()->getName();
        $sameProducts = $this->_product->getCollection()
        ->addAttributeToSelect('*')
        ->addFieldToFilter('name', $productName);
        
        return $sameProducts;
    }
    
}
