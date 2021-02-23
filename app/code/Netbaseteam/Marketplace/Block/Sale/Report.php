<?php

/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Sale;

class Report extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;


    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Marketplace\Model\SalesReportFactory $salesReportFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order\StatusFactory $orderStatusFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $formatDate,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        array $data = []
    )
    {
        $this->_salesReportFactory = $salesReportFactory;
        $this->_productFactory = $productFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        $this->_registry = $registry;
        $this->orderStatusFactory = $orderStatusFactory;
        $this->_formatDate = $formatDate;
        $this->timezone = $context->getLocaleDate();
        $this->logger = $context->getLogger();
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    public function getSalesReport()
    {
        $sellerId = $this->_helper->getSellerId();
        if (!($sellerId)) {
            return false;
        }

        $salesReportCollection = $this->_salesReportFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);
        $salesReportCollection->setOrder('id', 'DESC');
        return $salesReportCollection->addFieldToFilter('price', array('neq' => 0));
    }

    public function getOrderCollection($orderId) {
        return $orderCollection = $this->_orderCollectionFactory->create()->addFieldToFilter('entity_id', $orderId);
    }

    public function getOrderData($orderId){
        $orderCollection = $this->getOrderCollection($orderId);
        $order = array();
        foreach ($orderCollection as $item) {
            $order[] = $item->getData();
        }
        return $order[0];
    }

    public function getProduct($productId) {
        $product = $this->_productFactory->create()->load($productId);
        return $product;
    }

    public function getStatusLabel($code)
    {
        $status = $this->orderStatusFactory->create()->load($code);
        return $status->getLabel();
    }

    public function getOrderStatus() {
        $status = $this->orderStatusFactory->create()->getCollection();
        return $status;
    }

}
