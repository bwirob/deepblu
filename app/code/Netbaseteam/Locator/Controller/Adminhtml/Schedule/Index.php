<?php

namespace Netbaseteam\Locator\Controller\Adminhtml\Schedule;

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
        return $this->_authorization->isAllowed('Netbaseteam_Locator::locator_manage');
    }

    /**
     * Localtor List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Netbaseteam_Locator::locator_manage'
        )->addBreadcrumb(
            __('Locator'),
            __('Locator')
        )->addBreadcrumb(
            __('Manage Schedules'),
            __('Manage Schedules')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Schedules'));
        return $resultPage;
    }
}
