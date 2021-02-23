<?php

namespace Netbaseteam\Marketplace\Controller\Order;

use Magento\Framework\App\Action;
class Email extends \Magento\Framework\App\Action\Action
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
        $orderId =  $this->getRequest()->getParam('order_id');
        $order = '';
        if ($orderId) {
            $order = $this->_saleorder->create()->load($orderId);
        }

        if ($order) {
            try {
                $this->orderManagement->notify($order->getEntityId());
                $this->messageManager->addSuccess(__('You sent the order email.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t send the email order right now.'));
                $this->logger->critical($e);
            }
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/order/view',
                [
                    'order_id' => $order->getEntityId()
                ]
            );
        }
        return $this->resultRedirectFactory->create()->setPath('marketplace/sales/order');
    }
}
