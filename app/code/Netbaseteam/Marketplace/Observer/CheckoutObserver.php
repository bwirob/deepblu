<?php

namespace Netbaseteam\Marketplace\Observer;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepositoryInterface;
use Magento\Framework\Unserialize\Unserialize;
use Netbaseteam\Marketplace\Model\MarketplaceSaveCartFactory as MarketplaceSaveCartModelFactory;

class CheckoutObserver implements ObserverInterface
{
    /**
     * @var eventManager
     */
    protected $_eventManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * [$_coreSession description].
     *
     * @var SessionManager
     */
    protected $_coreSession;

    /**
     * @var QuoteRepository
     */
    protected $_quoteRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;


    /**
     * @var Unserialize
     */
    protected $_unserializer;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    protected $_marketplaceProductFactory;
    protected $cart;
    protected $_product;
    protected $formKey;
    protected $logger;
    protected $customerFactory;
    protected $sellerFactory;
    protected $itemPriceRenderer;
    protected $orderItemFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var MarketplaceSaveCartModelFactory
     */
    private $_saveCartModelFactory;

    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param SessionManager $coreSession
     * @param QuoteRepository $quoteRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param ProductRepositoryInterface $productRepository
     * @param Unserialize $unserializer
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        OrderRepositoryInterface $orderRepository,
        CheckoutSession $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Netbaseteam\Marketplace\Model\ProductFactory $marketplaceProductFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Magento\Weee\Block\Item\Price\Renderer $itemPriceRenderer,
        \Netbaseteam\Marketplace\Model\SellerFactory $sellerFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        MarketplaceSaveCartModelFactory $saveCartModelFactory,
        \Magento\Framework\Registry $registry,
        CustomerCart $cart,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\UrlInterface $urlInterface
    )
    {
        $this->_eventManager = $eventManager;
        $this->_objectManager = $objectManager;
        $this->_orderRepository = $orderRepository;
        $this->_marketplaceProductFactory = $marketplaceProductFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->itemPriceRenderer = $itemPriceRenderer;
        $this->sellerFactory = $sellerFactory;
        $this->customerFactory = $customerFactory;
        $this->_coreRegistry        = $registry;
        $this->productRepository = $productRepository;
        $this->_checkoutSession    = $checkoutSession;
        $this->_customerSession    = $customerSession;
        $this->logger = $logger;
        $this->_product = $product;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->_saveCartModelFactory = $saveCartModelFactory;
        $this->_date = $date;
        $this->_urlInterface = $urlInterface;
    }

    /**
     * Sales Order Place Success event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        $lastOrderId = $orderIds[0];
        $mpCollection = $this->_marketplaceProductFactory
            ->create()->getCollection();
        $mpProductIds = array();
        foreach ($mpCollection as $item) {
            $mpProductIds[] = $item->getProductId();
        }
        $orderItems = $this->orderItemFactory->create()->getCollection()->addFieldToFilter('order_id', $lastOrderId);
        $rowTotal = 0;
        $sellerAmount = 0;
        $countProduct = 0;
        $countSeller = 0;
        $seller_arr = array();
        foreach ($orderItems as $orderItem) {
            $sellerOrder = $this->_objectManager->create(
                'Netbaseteam\Marketplace\Model\Order'
            );
            $productId = $orderItem->getProductId();
            if (in_array($productId, $mpProductIds)) { /* foreach products in order */
                $countProduct += $orderItem->getQtyOrdered();
                $mpCollection1 = $this->_marketplaceProductFactory
                    ->create()->getCollection()
                    ->addFieldToFilter('status', 1)
                    ->addFieldToFilter('product_id', $productId);
                $sellerId = 0;
                foreach ($mpCollection1 as $item) { /* foreach seller */
                    $sellerId = $item->getSellerId();
                    if (!in_array($sellerId, $seller_arr)) {
                        $seller_arr[] = $sellerId;
                        $ret = $this->getProductInOrderOfSeller($productId, $lastOrderId, $sellerId);
                        $rowTotal = $ret["row_total"];
                        $countProduct = $ret["countProduct"];
                        $productIds = $ret["product_ids"];
                        $shippingMethods = $ret["shipping_methods"];
                        $shippingAmount = $ret["shippingAmount"];
                        $this->saveSellerOrder($lastOrderId, $sellerOrder,$sellerId, $productIds, $rowTotal , $countProduct, $shippingMethods, $shippingAmount);
                    }
                }
                $proPrice = $orderItem->getPrice();
                $earning = $this->itemPriceRenderer->getTotalAmount($orderItem);
                $countReport = $orderItem->getQtyOrdered();
                $this->saveSalesReport($lastOrderId, $sellerId, $proPrice, $earning, $countReport, $productId);
            }
        }

//      Add to Cart item remove
//        $customer_id = $this->_customerSession->getCustomerId();
//        $cart_saveCollection = $this->_saveCartModelFactory->create()->getCollection()
//                            ->addFilter('customer_id', $customer_id);
//        foreach ($cart_saveCollection as $cart_save){
//            $info_buyRequest = unserialize($cart_save->getQuoteItems());
//            foreach ($info_buyRequest as $info) {
//                $item = json_decode($info['info_buyRequest'], true);
//                $item['qty'] = $info['qty'];
//                if (isset($item['qty'])) {
//                    $filter = new \Zend_Filter_LocalizedToNormalized(
//                        ['locale' => $this->_objectManager->get(
//                            \Magento\Framework\Locale\ResolverInterface::class
//                        )->getLocale()]
//                    );
//                    $item['qty'] = $filter->filter($item['qty']);
//                }
//                $product = $this->_initProduct($item['product']);
//                $related = isset($item['related_product']) ? $item['related_product'] : "";
//                try {
//                    $this->cart->addProduct($product, $item);
//                } catch (\Exception $e){
//                }
//                if (!empty($related)) {
//                    $this->cart->addProductsByIds(explode(',', $related));
//                }
//            }
//        }
//        $this->cart->save();
        return $this;
    }

    public function getCheckoutSession()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
        $checkoutSession = $objectManager->get('Magento\Checkout\Model\Session');//checkout session
        return $checkoutSession;
    }

    public function getProductInOrderOfSeller($pid, $oid, $sellerID)
    {
        $mpCollection = $this->_marketplaceProductFactory->create()->getCollection();

        $mpProductIds = array();
        $ret = array();
        $orderItems = $this->orderItemFactory->create()->getCollection()->addFieldToFilter('order_id', $oid);

        foreach ($mpCollection as $item) {
            if ($sellerID == $item->getSellerId()) { /* foreach products in order */
                $mpProductIds[] = $item->getProductId();
            }
        }

        $row_total = 0; $countProduct = 0; $countReport= 0;
        $prouductList = '';
        foreach ($orderItems as $orderItem) {
            if(in_array($orderItem->getProductId(), $mpProductIds) && $orderItem->getQtyOrdered()){
                $prouductList .= ','.$orderItem->getProductId();
                $row_total += $this->itemPriceRenderer->getTotalAmount($orderItem);
                $proPrice = $orderItem->getPrice();
                if ($proPrice > 0) {
                    $countProduct += $orderItem->getQtyOrdered();
                    $countReport = $orderItem->getQtyOrdered();
                }
                $earning = $this->itemPriceRenderer->getTotalAmount($orderItem);
            }
        }

        $ret["row_total"] = $row_total;
        $ret["countProduct"] = $countProduct;
        $ret["product_ids"] = trim($prouductList,',');
        $ret["shipping_methods"] = "";
        $ret["shippingAmount"] = "";
        return $ret;
    }

    public function saveSellerOrder($lastOrderId,$sellerOrder, $sellerId, $productIds, $rowTotal, $countProduct, $shippingMethods, $shippingAmount) {
        $mpCollection2 = $this->sellerFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);
        $countSeller = 0;
        $commissionAmount = 0;
        $commissionType = 0;
        $fixedOrPecentage = 0;
        $sellerAmount = 0;
        $commission = 0;

        $seller = $this->customerFactory->create()->load($sellerId);

        $sellerName = '';
        if ($seller) {
            $sellerName = $seller->getName();
        }
        foreach ($mpCollection2 as $item) {
            $commissionAmount = $item->getCommissionAmount();
            $commissionType = $item->getCommissionType();
            $fixedOrPecentage = $item->getFixedOrPercentage();

            if ($fixedOrPecentage) {
                $sellerAmount = $rowTotal - $commissionAmount * $rowTotal / 100;
            } else {
                if ($commissionType) {
                    $sellerAmount = $rowTotal - $commissionAmount;
                } else {
                    $sellerAmount = $rowTotal - $commissionAmount * $countProduct;
                }
            }
            $commission = $rowTotal - $sellerAmount;

            $sellerOrder->setOrderId($lastOrderId);
            $sellerOrder->setSellerId($sellerId);
            $sellerOrder->setSellerName($sellerName);
            $sellerOrder->setProductIds($productIds);
            $sellerOrder->setShippingMethods($shippingMethods);
            $sellerOrder->setShippingAmount($shippingAmount);
            $sellerOrder->setPaidStatus('pending');
            $sellerOrder->setRowTotal($rowTotal);
            $sellerOrder->setSellerAmount($sellerAmount);
            $sellerOrder->setCommission($commission);
            $sellerOrder->save();

            $this->addOrderNotification($sellerOrder);

            $countSeller++;
        }
    }

    public function saveSalesReport($lastOrderId, $sellerId, $proPrice, $earning, $countReport, $productId) {
        $salesReport = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Model\SalesReport'
        );

        $salesReport->setOrderId($lastOrderId);
        $salesReport->setSellerId($sellerId);
        $salesReport->setProductId($productId);
        $salesReport->setPrice($proPrice);
        $salesReport->setEarning($earning);
        $salesReport->setQty($countReport);
        $salesReport->save();
    }

    protected function _initProduct($productId)
    {
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    protected function addOrderNotification($sellerOrder) {
        $notification = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Model\Notification'
        );

        $order = $this->_orderRepository->get($sellerOrder->getOrderId());
        $orderIncrementId = $order->getIncrementId();
        $url = $this->_urlInterface->getUrl('marketplace/order/view/', ['order_id' => $order->getId()]);
        
        $notification->setSellerId($sellerOrder->getSellerId());
        $notification->setCreatedAt($this->_date->gmtDate());
        $notification->setTitle('New Order');
        $notification->setDescription("A new order has arrived.Your order number is: #".$order->getIncrementId());
        $notification->setUrl($url);
        $notification->save();
    }
}