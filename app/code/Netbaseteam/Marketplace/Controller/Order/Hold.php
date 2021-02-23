<?php

namespace Netbaseteam\Marketplace\Controller\Order;

use Magento\Framework\App\Action;
class Hold extends \Magento\Framework\App\Action\Action
{
    /**
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Framework\Registry $registry
    ) {
        $this->_saleorder = $orderFactory;
        $this->orderManagement = $orderManagement;
        $this->_registry = $registry;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $orderId =  $this->getRequest()->getParam('order_id');
        $order = '';
        if ($orderId) {
            $order = $this->_saleorder->create()->load($orderId);
        }

        if ($order) {
            try {
                $this->orderManagement->hold($order->getEntityId());
                $this->messageManager->addSuccess(__('You put the order on hold.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('You have not put the order on hold.'));
            }
            $resultRedirect->setPath('marketplace/order/view', ['order_id' => $order->getId()]);
            return $resultRedirect;
        }
        $resultRedirect->setPath('marketplace/sales/order');
        return $resultRedirect;
    }
}
