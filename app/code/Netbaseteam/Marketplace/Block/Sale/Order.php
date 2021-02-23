<?php

/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Sale;

class Order extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orders;

    protected $_resource;


    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Marketplace\Model\OrderFactory $sellerOrderFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        array $data = []
    )
    {
        $this->_sellerOrderFactory = $sellerOrderFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        $this->_registry = $registry;
        $this->_resource = $resource;
        $this->logger = $context->getLogger();
        $this->_storeManager = $context->getStoreManager();
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

    /**
     * @return CollectionFactoryInterface
     *
     * @deprecated
     */
    public function getSellerOrderCollection()
    {
        $sellerId = $this->_helper->getSellerId();
        return $this->_sellerOrderFactory->create()->getCollection()->addFieldToFilter('seller_id',$sellerId);
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        $sellerId = $this->_helper->getSellerId();
        if (!($sellerId)) {
            return false;
        }

        $sellerOrderCollection = $this->getSellerOrderCollection();

        $orderIds = array();
        foreach ($sellerOrderCollection as $item ) {
            array_push($orderIds, $item->getOrderId());
        }

        $orderCollection = $this->_orderCollectionFactory->create()
            ->addFieldToFilter('entity_id', array('in' => $orderIds));
        $orderCollection->setOrder('increment_id', 'DESC');
        
        $second_table_name = $this->_resource->getTableName('netbaseteam_marketplace_order');
        $orderCollection->getSelect()->join( array('second'=> $second_table_name),
            'second.order_id = main_table.entity_id', array('second.row_total'));

        $orderCollection->addFieldToFilter('seller_id', array('in' => $sellerId));
        return $orderCollection;
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('marketplace/order/view/', ['order_id' => $order->getId()]);
    }

}
