<?php

namespace Netbaseteam\Marketplace\Controller\Locator;

use Magento\Framework\View\Result\PageFactory;

class Delete extends \Magento\Framework\App\Action\Action
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
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $sellerLocator = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Location');
                    $sellerLocator->load($id);

                    try {
                        $sellerLocator->delete();

                        $this->messageManager->addSuccess(
                            __('Seller locator was successfully deleted!')
                        );
                    } catch (\Exception $e) {
                        $this->messageManager->addException($e, __('We can\'t delete the locator.'));
                    }
                }
            } else {
                return $this->resultRedirectFactory->create()
                    ->setPath('marketplace/seller/editprofile', ['_secure' => $this->getRequest()->isSecure()]);
            }

            return $this->resultRedirectFactory->create()->setPath(
                '*/seller/editProfile',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        } else {
            return $this->resultRedirectFactory->create()
                ->setPath('marketplace/account/registry', ['_secure' => $this->getRequest()->isSecure()]);
        }
    }
}