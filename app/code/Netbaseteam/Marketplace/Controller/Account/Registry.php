<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Account;

use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Registry extends \Magento\Framework\App\Action\Action
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
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry
    )
    {
        $this->customerSession = $customerSession;
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * Display marketplace form registry
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        if ($this->customerSession->isLoggedIn()) {
            $this->_registry->register('current_customer',$this->customerSession->getId());
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'customer/account/login/',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
