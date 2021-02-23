<?php

namespace Netbaseteam\Marketplace\Controller\Locator;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Marketplace Seller Locator Edit controller.
 */
class Edit extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Marketplace Seller's Locator Page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        $isLocator = $helper->isLocatorEnable();

        if ($isPartner) {
            if ($isLocator) {
                $resultPage = $this->_resultPageFactory->create();

                return $resultPage;
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
