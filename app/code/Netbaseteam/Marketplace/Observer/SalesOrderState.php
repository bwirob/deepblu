<?php

namespace Netbaseteam\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Unserialize\Unserialize;


class SalesOrderState implements ObserverInterface
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
        \Netbaseteam\Marketplace\Helper\Data $marketplaceHelperData,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Netbaseteam\Marketplace\Model\OrderFactory $mpOrderFactory,
        \Netbaseteam\Marketplace\Model\SalesReportFactory $mpSalesReportFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Url $urlBuilder
    )
    {
        $this->_eventManager = $eventManager;
        $this->_objectManager = $objectManager;
        $this->_marketplaceHelperData = $marketplaceHelperData;
        $this->orderFactory = $orderFactory;
        $this->mpOrderFactory = $mpOrderFactory;
        $this->mpSalesReportFactory = $mpSalesReportFactory;
        $this->_storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->customerFactory = $customerFactory;
        $this->urlBuilder = $urlBuilder;

    }

    /**
     * Sales Order Place Success event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getId();

        if ($order instanceof \Magento\Framework\Model\AbstractModel) {
            if ($order->getState() == 'complete') {
                if ($orderId) {
                    $mpOrderCollection = $this->mpOrderFactory->create()->getCollection()->addFieldToFilter('order_id', $orderId);
                    $mpSalesReportCollection = $this->mpSalesReportFactory->create()->getCollection()->addFieldToFilter('order_id', $orderId);

                    $mpOrderId = 0;
                    foreach ($mpOrderCollection as $item) {
                        $mpOrder = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Order');
                        $mpOrderId = $item->getId();

                        if ($mpOrderId) {
                            $mpOrder->load($mpOrderId);
                            try {
                                $sellerId = $mpOrder->getSellerId();
                                $commisionAmount = $mpOrder->getCommission();
                                $sellerAmount = $mpOrder->getSellerAmount();
                                $seller = $this->_objectManager->create(
                                    'Netbaseteam\Marketplace\Model\Seller'
                                )
                                    ->getCollection()
                                    ->addFieldToFilter(
                                        'seller_id',
                                        $sellerId
                                    );

                                $sellerData = $this->_objectManager->create(
                                    'Netbaseteam\Marketplace\Model\Sellerdata'
                                )
                                    ->getCollection()
                                    ->addFieldToFilter(
                                        'seller_id',
                                        $sellerId
                                    );

                                $totalSellerAmount = 0;
                                $totalCommissionAmount = 0;
                                $sellerEntityId = 0;
                                foreach ($seller as $item) {
                                    $totalSellerAmount = $item->getTotalSellerAmount();
                                    $totalCommissionAmount = $item->getTotalCommissionAmount();
                                    $sellerEntityId = $item->getId();
                                }
                                $totalSellerAmount = $totalSellerAmount + trim($sellerAmount, $this->getCurrencySymbol());
                                $totalCommissionAmount = $totalCommissionAmount + trim($commisionAmount, $this->getCurrencySymbol());

                                $sellerModel = $this->_objectManager->create(
                                    'Netbaseteam\Marketplace\Model\Seller'
                                )->load($sellerEntityId);

                                $shopTitle = '';
                                foreach ($sellerData as $item) {
                                    $shopTitle = $item->getShopTitle();
                                }

                                $customer = $this->customerFactory->create()->load($sellerId);

                                $transaction = $this->_objectManager->create(
                                    'Netbaseteam\Marketplace\Model\Transaction'
                                );

                                $mpOrder->setPaidStatus('paid');
                                $mpOrder->save();

                                $sellerModel->setTotalSellerAmount($totalSellerAmount);
                                $sellerModel->setTotalCommissionAmount($totalCommissionAmount);
                                $sellerModel->save();

                                $transaction->setSellerId($sellerId);
                                $transaction->setShopTitle($shopTitle);
                                $transaction->setOrderId($mpOrder->getOrderId());
                                $transaction->setAmount(trim($sellerAmount, $this->getCurrencySymbol()));
                                $transaction->save();

                                $this->sendMail($mpOrder);
                                $this->messageManager->addSuccess(__('Payment has been successfully done for seller '.$customer->getName().''));
                            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                                $this->messageManager->addError($e->getMessage());
                            } catch (\RuntimeException $e) {
                                $this->messageManager->addError($e->getMessage());
                            } catch (\Exception $e) {
                                $this->messageManager->addException($e, __('Something went wrong while paying for seller.'));
                            }
                        }

                    }

                }
            }
        }
        return $this;

    }

    private function sendMail($data)
    {
        $helper = $this->_marketplaceHelperData;
        $sellerName = '';
        $sellerEmail = '';
        $sellerId = '';
        $orderId = '';

        if ($data) {
            $sellerId = $data['seller_id'];
            $orderId = $data['order_id'];
            $sellerAmount = $data['seller_amount'];
            $commisionAmount = $data['commission'];
        }

        $order = $this->orderFactory->create()->load($orderId);
        $orderIncrement = $order->getIncrementId();

        if ($sellerId) {
            $customer = $this->_objectManager->get(
                'Magento\Customer\Model\Customer'
            )->load($sellerId);

            $sellerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $sellerEmail = $customer->getEmail();
        }


        $emailTempVariables = [];
        $adminStoremail = $helper->getAdminEmailId();
        $adminEmail = $adminStoremail ?
            $adminStoremail : $helper->getDefaultTransEmailId();
        $adminUsername = 'Admin';

        $reportUrl = $this->urlBuilder->getUrl(
            'marketplace/order/report/'
        );

        $emailTempVariables['admin'] = $adminUsername;
        $emailTempVariables['templateSubject'] = "Pay for Seller";
        $emailTempVariables['report_url'] = $reportUrl;
        $emailTempVariables['order'] = $orderIncrement;
        $emailTempVariables['seller_amount'] = $sellerAmount;
        $emailTempVariables['commission_amount'] = $commisionAmount;

        $senderInfo = [
            'name' => $adminUsername,
            'email' => $adminEmail,
        ];
        $receiverInfo = [
            'name' => $sellerName,
            'email' => $sellerEmail,
        ];

        $helper->sendPaysellerMail(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo
        );
    }

    public function getCurrencySymbol()
    {
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $currency = $this->_objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode);
        return $currencySymbol = $currency->getCurrencySymbol();
    }
}