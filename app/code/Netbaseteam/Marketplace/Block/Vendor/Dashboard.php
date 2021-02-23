<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Vendor;

class Dashboard extends \Magento\Framework\View\Element\Template
{
    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Marketplace\Model\OrderFactory $sellerOrderFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Netbaseteam\Marketplace\Model\TransactionFactory $transactionFactory,
        \Netbaseteam\Marketplace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        array $data = []
    )
    {
        $this->_sellerOrderFactory = $sellerOrderFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->orderFactory = $orderFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->_registry = $registry;
        $this->sellerFactory = $sellerFactory;
        $this->_storeManager = $context->getStoreManager();
        $this->localeCurrency = $localeCurrency;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {

        parent::_prepareLayout();
        return $this;
    }

    public function getOrders()
    {
        $sellerId = $this->_helper->getSellerId();
        if (!($sellerId)) {
            return false;
        }

        $sellerOrderCollection = $this->_sellerOrderFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);
        $orderIds = array();
        foreach ($sellerOrderCollection as $item) {
            array_push($orderIds, $item->getOrderId());
        }

        $orderCollection = $this->_orderCollectionFactory->create()
            ->addFieldToFilter('entity_id', array('in' => $orderIds))
            ->setOrder('entity_id');

        return $orderCollection;
    }

    public function getLastOrders()
    {
        $orderCollection = $this->getOrders()->setOrder('entity_id', 'DESC')->setPageSize(5);
        return $orderCollection;
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($orderId)
    {
        return $this->getUrl('marketplace/order/view/', ['order_id' => $orderId]);
    }

    public function getOrderData()
    {
        $data = array();
        $reportrange = $this->getRequest()->getParam('reportrange');
        if ($reportrange) {
            $times = explode("-",$reportrange);

            if (isset($times[0]) && isset($times[1])) {
                $dateFrom = date('Y-m-d', strtotime($times[0]));
                $dateTo   = date('Y-m-d', strtotime($times[1]));
                $datediff = date_diff(date_create($dateFrom), date_create($dateTo))->format('%a');

                for($i=$datediff;$i>=0;$i--){
                    $order = $this->getOrders();
                    $date = date_create($dateTo);
                    date_add($date, date_interval_create_from_date_string(''.-$i.' days'));

                    array_push($data, [date_timestamp_get($date)*1000, count($order->addFieldToFilter('created_at', array('like' => date_format($date, 'Y-m-d') . '%')))]);
                }
            }
            
        } else {
            $dateTo = date('Y-m-d');
            $datediff = 7;
            for($i=$datediff;$i>=0;$i--){
                $order = $this->getOrders();
                $date = date_create($dateTo);
                date_add($date, date_interval_create_from_date_string(''.-$i.' days'));

                array_push($data, [date_timestamp_get($date)*1000, count($order->addFieldToFilter('created_at', array('like' => date_format($date, 'Y-m-d') . '%')))]);
            }
        }

        return $data;
    }

    public function getTotalRevenue()
    {
        $data = array();
        $reportrange = $this->getRequest()->getParam('reportrange');
        if ($reportrange) {
            $times = explode("-",$reportrange);

            if (isset($times[0]) && isset($times[1])) {
                $dateFrom = date('Y-m-d', strtotime($times[0]));
                $dateTo   = date('Y-m-d', strtotime($times[1]));
                $datediff = date_diff(date_create($dateFrom), date_create($dateTo))->format('%a');

                for($i=$datediff;$i>=0;$i--){
                    $order = $this->getOrders();
                    $date = date_create($dateTo);
                    date_add($date, date_interval_create_from_date_string(''.-$i.' days'));
                    
                    $orderData = $order->addFieldToFilter('created_at', array('like' => date_format($date, 'Y-m-d') . '%'))->getData();

                    if (!empty($orderData)) {
                        array_push($data, $order->addFieldToFilter('created_at', array('like' => date_format($date, 'Y-m-d') . '%'))->getData()[0]['grand_total']);
                    } else {
                        array_push($data, 0);
                    }
                    
                }
            }
            
        } else {
            $dateTo = date('Y-m-d');
            $datediff = 7;
            for($i=$datediff;$i>=0;$i--){
                $order = $this->getOrders();
                $date = date_create($dateTo);
                date_add($date, date_interval_create_from_date_string(''.-$i.' days'));

                $orderData = $order->addFieldToFilter('created_at', array('like' => date_format($date, 'Y-m-d') . '%'))->getData();

                if (!empty($orderData)) {
                    array_push($data, $order->addFieldToFilter('created_at', array('like' => date_format($date, 'Y-m-d') . '%'))->getData()[0]['grand_total']);
                } else {
                    array_push($data, 0);
                }
            }
        }

        return $data;
    }

    public function getLifetimeSales()
    {
        $order = $this->getOrders();
        $lifetimeSales = 0;
        foreach ($order as $item) {
            $lifetimeSales = $lifetimeSales + $item['grand_total'];
        }

        return $lifetimeSales;
    }

    public function getAverageOrder()
    {
        $order = $this->getOrders();

        $totalOrder = 0;
        $averageOrder = 0;
        foreach ($order as $item) {
            $totalOrder = $totalOrder + $item['base_grand_total'];
        }
        if (count($order)) {
            $averageOrder = $totalOrder / count($order);
        }

        return $averageOrder;
    }

    public function getCommission() {
        $sellerId = $this->_helper->getSellerId();
        if (!($sellerId)) {
            return false;
        }

        $store = $this->_storeManager->getStore();
        $currency = $this->localeCurrency->getCurrency($store->getBaseCurrencyCode());


        $seller = $this->sellerFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);
        $commissionType = 0;
        $commissionOption = 0;
        foreach ($seller as $item) {
            $commissionType = $item->getCommissionType();
            $commissionOption = $item->getFixedOrPercentage();
            $commissionAmount = $item->getCommissionAmount();
        }

        $this->_registry->register("commision_type", $commissionType);

        if ($commissionOption) {
            $commissionAmount = (int)$commissionAmount.'%';
        } else {
            $commissionAmount = $currency->toCurrency(sprintf("%f", $commissionAmount));
        }

        return $commissionAmount;

    }

    public function getCommissionType() {
       return $this->_registry->registry('commision_type');
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

        $order = $this->_sellerOrderFactory->create()->getCollection()
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

    public function getCurrencySymbol()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode);
        return $currencySymbol = $currency->getCurrencySymbol();
    }
}
