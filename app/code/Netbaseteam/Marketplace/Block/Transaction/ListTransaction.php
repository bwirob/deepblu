<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Transaction;

class ListTransaction extends \Magento\Framework\View\Element\Template
{
    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Marketplace\Model\TransactionFactory $transactionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $contextInterface,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Netbaseteam\Marketplace\Model\OrderFactory $mpOrderFactory,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->_transactionFactory = $transactionFactory;
        $this->storeManager = $context->getStoreManager();
        $this->context = $contextInterface;
        $this->localeCurrency = $localeCurrency;
        $this->orderFactory = $orderFactory;
        $this->mpOrderFactory = $mpOrderFactory;
        $this->_logger = $context->getLogger();
        $this->_localeDate = $context->getLocaleDate();
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {

        parent::_prepareLayout();
        if ($this->getTransaction()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'marketplace.sales.report.pager'
            )->setAvailableLimit(array(10 => 10))
                ->setShowPerPage(true)->setCollection(
                    $this->getTransaction()
                );
            $this->setChild('pager', $pager);
            $this->getTransaction()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getTransaction()
    {
        $sellerId = $this->_helper->getSellerId();
        $transaction = $this->_transactionFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);

        return $transaction;
    }

    public function getTotalTransaction()
    {
        $sellerId = $this->_helper->getSellerId();
        $orderComplete = $this->orderFactory->create()->getCollection()->addFieldToFilter('status', 'complete');

        $orderIds = array();
        foreach ($orderComplete as $item) {
            array_push($orderIds, $item->getEntityId());
        }

        $order = $this->mpOrderFactory->create()->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->addFieldToFilter('order_id', array('in' => $orderIds));

        $totalTransaction = 0;
        foreach ($order as $item) {
            $totalTransaction = $totalTransaction + $item['row_total'];
        }

        return $totalTransaction;
    }

    public function getReceivedTransaction()
    {
        $transaction = $this->getTransaction();

        $receivedTransaction = 0;
        foreach ($transaction as $item) {
            $receivedTransaction = $receivedTransaction + $item['amount'];
        }

        return $receivedTransaction;
    }

    public function getRemainTransaction()
    {
        $totalTransaction = $this->getTotalTransaction();
        $receivedTransaction = $this->getReceivedTransaction();
        return $totalTransaction - $receivedTransaction;
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

    public function getOrderIncrement($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);
        return $order->getIncrementId();
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($transaction)
    {
        return $this->getUrl('marketplace/transaction/view/', ['id' => $transaction->getId()]);
    }

}
