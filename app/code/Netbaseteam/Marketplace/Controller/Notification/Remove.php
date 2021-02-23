<?php

namespace Netbaseteam\Marketplace\Controller\Notification;

use Magento\Framework\View\Result\PageFactory;

class Remove extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;

        parent::__construct($context);
    }

    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();

        if ($isPartner) {
            $notification = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Notification');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $notification->load($id);
            }

            try {
                $notification->delete();

                $this->messageManager->addSuccess(
                    __('The message has been removed.')
                );
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t remove the message.'));
            }

            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/notification/index',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        } else {
            return $this->resultRedirectFactory->create()
                ->setPath('marketplace/account/registry', ['_secure' => $this->getRequest()->isSecure()]);
        }

    }
}