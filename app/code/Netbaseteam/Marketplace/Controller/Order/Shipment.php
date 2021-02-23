<?php

namespace Netbaseteam\Marketplace\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

class Shipment extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @param Action\Context $context
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        PageFactory $resultPageFactory
    ) {
        $this->shipmentLoader = $shipmentLoader;
        $this->registry = $registry;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Shipment create page
     *
     * @return void
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
        if (!$order->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
        }

        $this->shipmentLoader->setOrderId($this->getRequest()->getParam('order_id'));
        $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
        $this->shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
        $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
        $shipment = $this->shipmentLoader->load();
        if ($shipment) {
            $comment = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getCommentText(true);
            if ($comment) {
                $shipment->setCommentText($comment);
            }

            $this->registry->register('current_order', $order);

            $resultPage = $this->_resultPageFactory->create();
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('marketplace/order/view', ['order_id' => $this->getRequest()->getParam('order_id')]);
        }
    }
}
