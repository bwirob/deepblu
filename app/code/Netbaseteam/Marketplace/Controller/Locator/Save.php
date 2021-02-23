<?php

namespace Netbaseteam\Marketplace\Controller\Locator;

use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Framework\App\Action\Action
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
        $isLocator = $helper->isLocatorEnable();

        if ($isPartner) {
            if ($isLocator) {
                $sellerId = $this->_customerSession->getCustomerId();
                $data = $this->getRequest()->getPostValue();

                if ($data) {
                    $sellerLocator = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Location');
                    $id = $this->getRequest()->getParam('id');
                    if ($id) {
                        $sellerLocator->load($id);
                    }

                    $sellerLocator->setData($data);
                    $sellerLocator->setSellerId($sellerId);

                    try {
                        $sellerLocator->save();

                        $this->messageManager->addSuccess(
                            __('Seller locator was successfully saved!')
                        );
                    } catch (\Exception $e) {
                        $this->messageManager->addException($e, __('We can\'t save the locator.'));
                    }
                }

                return $this->resultRedirectFactory->create()->setPath(
                    '*/seller/editProfile',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            } else {
                return $this->resultRedirectFactory->create()
                    ->setPath('marketplace/seller/editprofile', ['_secure' => $this->getRequest()->isSecure()]);
            }
        } else {
            return $this->resultRedirectFactory->create()
                ->setPath('marketplace/account/registry', ['_secure' => $this->getRequest()->isSecure()]);
        }

    }
}