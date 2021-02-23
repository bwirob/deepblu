<?php

namespace Netbaseteam\Shopbybrand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Shopbybrand::shopbybrand_manage');
    }

    /**
     * Shopbybrand List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Netbaseteam_Shopbybrand::shopbybrand_manage'
        )->addBreadcrumb(
            __('Shopbybrand'),
            __('Shopbybrand')
        )->addBreadcrumb(
            __('Manage Shopbybrand'),
            __('Manage Shopbybrand')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Brands'));
        return $resultPage;
    }
}
