<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Transaction;

use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Registry
     */

    protected $_registry;

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory

    )
    {
        $this->_saleorder = $orderFactory;
        $this->_registry = $registry;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Display marketplace product
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        if ($isPartner) {
            $transactionId =  $this->getRequest()->getParam('id');
            $transaction = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Transaction')->load($transactionId);

            $this->_registry->register('transaction', $transaction);

            /** @var \Magento\Framework\View\Result\Page resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->getConfig()->getTitle()->set(
                __('Transaction Details')
            );
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()
                ->setPath('marketplace/account/registry', ['_secure' => $this->getRequest()->isSecure()]);
        }

    }
}
