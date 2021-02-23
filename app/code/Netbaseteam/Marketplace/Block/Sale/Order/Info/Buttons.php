<?php

/**
 * Block of links in Order view page
 */
namespace Netbaseteam\Marketplace\Block\Sale\Order\Info;

use Magento\Customer\Model\Context;

class Buttons extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'sale/order/info/buttons.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Magento\Sales\Model\Order\Shipment $shipment,
        \Magento\Sales\Model\Order\Creditmemo $creditmemo,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Netbaseteam\Marketplace\Model\ProductFactory $mpProductFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->_invoice = $invoice;
        $this->_creditmemo = $creditmemo;
        $this->_shipment = $shipment;
        $this->_productFactory = $productFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->_customerSession = $customerSession;
        $this->_mpProductFactory = $mpProductFactory;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    public function getMpProducts() {
        $sellerId = $this->_customerSession->getCustomerId();
        $collection = $this->_mpProductFactory->create()->getCollection()
            ->addFieldToFilter('seller_id', $sellerId);
        $allIds = array();

        foreach ($collection as $item) {
            $allIds[] = $item->getProductId();
        }

        return $allIds;
    }

    public function isInvoice() {
        $invoice = $this->_invoice->getCollection()->addFieldToFilter('order_id',$this->getOrder()->getId());

        if (!empty($invoice->getAllIds())) {
            return true;
        } else {
            return false;
        }
    }

    public function isCreditmemo() {
        $creditmemo = $this->_creditmemo->getCollection()->addFieldToFilter('order_id',$this->getOrder()->getId());

        if (!empty($creditmemo->getAllIds())) {
            return true;
        } else {
            return false;
        }
    }

    public function isShipment() {
        $order = $this->getOrder();
        $shipment = $this->_shipment->getCollection()->addFieldToFilter('order_id',$order->getId());

        if (!empty($shipment->getAllIds())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get url for printing order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    public function getPrintUrl($order)
    {
        return $this->getUrl('marketplace/order/print', ['order_id' => $order->getId()]);
    }

    public function getCancelUrl($order)
    {
        return $this->getUrl('marketplace/order/cancel', ['order_id' => $order->getId()]);
    }

    public function getSendMailUrl($order)
    {
        return $this->getUrl('marketplace/order/email', ['order_id' => $order->getId()]);
    }

    public function getHoldUrl($order)
    {
        return $this->getUrl('marketplace/order/hold', ['order_id' => $order->getId()]);
    }

    public function getUnHoldUrl($order)
    {
        return $this->getUrl('marketplace/order/unhold', ['order_id' => $order->getId()]);
    }

    public function getInvoiceUrl($order)
    {
        return $this->getUrl('marketplace/order/invoice', ['order_id' => $order->getId()]);
    }

    public function getCreditMemoUrl($order)
    {
        return $this->getUrl('marketplace/order/creditmemo', ['order_id' => $order->getId()]);
    }

    public function getShipUrl($order)
    {
        return $this->getUrl('marketplace/order/shipment', ['order_id' => $order->getId()]);
    }

    public function getPdfInvoiceUrl($order)
    {
        return $this->getUrl('marketplace/order/pdfinvoices', ['order_id' => $order->getId()]);
    }

    public function getBackUrl()
    {
        return $this->getUrl('marketplace/sales/order');
    }

}
