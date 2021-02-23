<?php

namespace Netbaseteam\Marketplace\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

class Creditmemo extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;


    /**
     * @param Context $context
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        PageFactory $resultPageFactory
    ) {
        $this->creditmemoLoader = $creditmemoLoader;
        $this->registry = $registry;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
        if (!$order->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
        }

        $this->creditmemoLoader->setOrderId($this->getRequest()->getParam('order_id'));
        $this->creditmemoLoader->setCreditmemoId($this->getRequest()->getParam('creditmemo_id'));
        $this->creditmemoLoader->setCreditmemo($this->getRequest()->getParam('creditmemo'));
        $this->creditmemoLoader->setInvoiceId($this->getRequest()->getParam('invoice_id'));
        $creditmemo = $this->creditmemoLoader->load();
        if ($creditmemo) {
            if ($comment = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getCommentText(true)) {
                $creditmemo->setCommentText($comment);
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
