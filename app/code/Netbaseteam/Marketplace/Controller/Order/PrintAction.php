<?php

namespace Netbaseteam\Marketplace\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

abstract class PrintAction extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    protected $orderLoader;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Controller\AbstractController\OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    )
    {
        $this->orderLoader = $orderLoader;
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Order details print page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        if ($isPartner) {
            $orderId = $this->getRequest()->getParam('order_id');

            $order = $this->_objectManager->create(
                'Magento\Sales\Model\Order'
            )->load($orderId);

            if ($order) {
                $this->_coreRegistry->register('current_order', $order);
                /** @var \Magento\Framework\View\Result\Page $resultPage */
                $resultPage = $this->resultPageFactory->create();

                $resultPage->getConfig()->getTitle()->set(
                    __('Order #%1', $order->getRealOrderId())
                );
                $resultPage->addHandle('print');

                return $resultPage;
            } else {
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
                    $resultRedirect->setPath('marketplace/sales/order');
                } else {
                    $resultRedirect->setPath('customer/account/login/');
                }
                return $resultRedirect;
            }

        } else {
            return $this->resultRedirectFactory->create()
                ->setPath('marketplace/account/registry', ['_secure' => $this->getRequest()->isSecure()]);
        }
    }
}
