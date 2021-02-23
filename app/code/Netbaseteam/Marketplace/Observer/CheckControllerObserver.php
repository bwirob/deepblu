<?php
namespace Netbaseteam\Marketplace\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface as CartRepositoryInterface;
use Netbaseteam\Marketplace\Model\MarketplaceSaveCartFactory as MarketplaceSaveCartModelFactory;

class CheckControllerObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var MarketplaceSaveCartModelFactory
     */
    private $_saveCartModelFactory;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CustomerCart
     */
    protected $cart;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    /**
     * CheckControllerObserver constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param MarketplaceSaveCartModelFactory $saveCartModelFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CustomerCart $cart
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        CartRepositoryInterface $quoteRepository,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        MarketplaceSaveCartModelFactory $saveCartModelFactory,
        ProductRepositoryInterface $productRepository,
        CustomerCart $cart
    ) {
        $this->_customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->_saveCartModelFactory = $saveCartModelFactory;
        $this->redirect = $redirect;
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
        $this->productRepository = $productRepository;
        $this->_objectManager = $objectManager;
        $this->cart = $cart;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerId= $this->_customerSession->getId();
        if ($customerId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
            $currentFullActionName = $request->getFullActionName();
            if ($currentFullActionName == 'checkout_cart_index') {
                //Add to Cart item remove
                $cart_saveCollection = $this->_saveCartModelFactory->create()->getCollection()->addFilter('customer_id', $customerId);
                foreach ($cart_saveCollection as $cart_save) {
                    $info_buyRequest = unserialize($cart_save->getQuoteItems());
                    foreach ($info_buyRequest as $info) {
                        $item = json_decode($info['info_buyRequest'], true);
                        $item['qty'] = $info['qty'];
                        if (isset($item['qty'])) {
                            $filter = new \Zend_Filter_LocalizedToNormalized(
                                ['locale' => $this->_objectManager->get(
                                    \Magento\Framework\Locale\ResolverInterface::class
                                )->getLocale()]
                            );
                            $item['qty'] = $filter->filter($item['qty']);
                        }
                        $product = $this->_initProduct($item['product']);
                        $related = isset($item['related_product']) ? $item['related_product'] : "";
                        try {
                            $this->cart->addProduct($product, $item);
                        } catch (\Exception $e) {
                        }
                        if (!empty($related)) {
                            $this->cart->addProductsByIds(explode(',', $related));
                        }
                    }
                    $this->cart->save();
                    $cart_save->delete($cart_save->getCustomerId());
                }
            }
        }
        return $this;
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
}