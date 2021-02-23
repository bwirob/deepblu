<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Transaction;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Netbaseteam\Marketplace\Model\OrderFactory $mpOrderFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $contextInterface,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->orderFactory = $orderFactory;
        $this->customerSession = $customerSession;
        $this->mpOrderFactory = $mpOrderFactory;
        $this->storeManager = $context->getStoreManager();
        $this->context = $contextInterface;
        $this->localeCurrency = $localeCurrency;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {

        parent::_prepareLayout();
        return $this;
    }

    public function getTransaction () {
        return $this->_registry->registry('transaction');
    }

    public function getOrderIncrement()
    {
        $orderId = $this->getOrderId();
        $order = $this->orderFactory->create()->load($orderId);

        return $order->getIncrementId();
    }

    public function getOrderCreateAt()
    {
        $orderId = $this->getOrderId();
        $order = $this->orderFactory->create()->load($orderId);

        return $order->getCreatedAt();
    }

    public function getOrderId () {
        $transaction = $this->getTransaction();
        $orderId = 0;
        foreach ($transaction as $item) {
            $orderId = $item['order_id'];
        }

        return $orderId;
    }

    public function getMpOrder () {
        $orderId = $this->getOrderId();
        $order = $this->mpOrderFactory->create()->getCollection()
            ->addFieldToFilter('seller_id', $this->customerSession->getCustomerId())
            ->addFieldToFilter('order_id', $orderId);

        return $order;
    }

    public function getAmount($amount)
    {
        $store = $this->storeManager->getStore(
            $this->context->getFilterParam('store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
        );

        $currency = $this->localeCurrency->getCurrency($store->getBaseCurrencyCode());
        $transactionAmount = $currency->toCurrency(sprintf("%f", $amount));

        return $transactionAmount;
    }
}
