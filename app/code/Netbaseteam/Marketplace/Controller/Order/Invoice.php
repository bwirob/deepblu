<?php

namespace Netbaseteam\Marketplace\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Netbaseteam\Marketplace\Model\Service\InvoiceService;

class Invoice extends \Magento\Framework\App\Action\Action
{
        /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param InvoiceService $invoiceService
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory,
        InvoiceService $invoiceService
    ) {
        $this->registry = $registry;
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Redirect to order view page
     *
     * @param int $orderId
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function _redirectToOrder($orderId)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('marketplace/order/view', ['order_id' => $orderId]);
    }

    /**
     * Invoice create page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $invoiceData = $this->getRequest()->getParam('invoice', []);
        $invoiceItems = isset($invoiceData['items']) ? $invoiceData['items'] : [];

        try {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
            if (!$order->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
            }

            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The order does not allow an invoice to be created.')
                );
            }
            $invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);
            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an invoice without products.')
                );
            }
            $this->registry->register('current_invoice', $invoice);
            $this->registry->register('current_order', $order);

            $comment = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getCommentText(true);
            if ($comment) {
                $invoice->setCommentText($comment);
            }

            $resultPage = $this->_resultPageFactory->create();
            return $resultPage;
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->messageManager->addError($exception->getMessage());
            return $this->_redirectToOrder($orderId);
        } catch (\Exception $exception) {
            $this->messageManager->addException($exception, 'Cannot create an invoice.');
            return $this->_redirectToOrder($orderId);
        }
    }
}
