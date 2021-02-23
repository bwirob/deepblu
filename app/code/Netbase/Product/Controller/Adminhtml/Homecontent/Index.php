<?php

namespace Netbase\Product\Controller\Adminhtml\Homecontent;

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
        return $this->_authorization->isAllowed('Netbase_Product::product_manage');
    }

    /**
     * Product List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Netbase_Product::product_manage'
        )->addBreadcrumb(
            __('Section Type Content'),
            __('Section Type Content')
        )->addBreadcrumb(
            __('Manage Section Type Content'),
            __('Manage Section Type Content')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Section Type Content'));
        return $resultPage;
    }
}
