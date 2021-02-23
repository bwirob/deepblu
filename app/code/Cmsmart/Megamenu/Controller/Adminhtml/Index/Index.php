<?php

namespace Cmsmart\Megamenu\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Cmsmart_Megamenu::megamenu_manage');
    }

    /**
     * Megamenu List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Cmsmart_Megamenu::megamenu_manage'
        )->addBreadcrumb(
            __('Megamenu'),
            __('Megamenu')
        )->addBreadcrumb(
            __('Manage Megamenu'),
            __('Manage Megamenu')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Megamenu'));
        return $resultPage;
    }
}
