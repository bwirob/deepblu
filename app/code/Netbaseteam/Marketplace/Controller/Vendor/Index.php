<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Vendor;

use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Registry
     */

    protected $_registry;

    protected $_helper;

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Marketplace\Model\VacationFactory $vacationFactory,
        \Netbaseteam\Marketplace\Helper\Data $helper
    ) {
        $this->customerSession = $customerSession;
        $this->_registry = $registry;
        $this->vacationFactory = $vacationFactory;
        parent::__construct($context);
        $this->_helper = $helper;
    }

    /**
     * Display marketplace vendor panel
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        $helper = $this->_helper;
        $helper->getMessageLicenseFrontend();
        $isPartner = $helper->getSellerId();

        $isVacation = $helper->isVacation();
        if ($isPartner) {
            /** @var \Magento\Framework\View\Result\Page resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->getConfig()->getTitle()->set(
                __('Vendor Panel')
            );
            
            return $resultPage;

        } else {
            return $this->resultRedirectFactory->create()
                ->setPath('marketplace/account/registry', ['_secure' => $this->getRequest()->isSecure()]);
        }
    }
}
