<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Product\Attribute;

use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Listattribute extends \Magento\Framework\App\Action\Action
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
        \Magento\Customer\Model\Session $customerSession

    )
    {
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
            $this->_registry->register('sellerId', $this->customerSession->getCustomer()->getId());

            /** @var \Magento\Framework\View\Result\Page resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->getConfig()->getTitle()->set(
                __('Attributes')
            );
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()
                ->setPath('marketplace/account/registry', ['_secure' => $this->getRequest()->isSecure()]);
        }

    }
}
