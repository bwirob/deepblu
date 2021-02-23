<?php

namespace Netbaseteam\Orderupload\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Netbaseteam\Orderupload\Controller\AbstractController\OrderuploadLoaderInterface
     */
    protected $orderuploadLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, OrderuploadLoaderInterface $orderuploadLoader, PageFactory $resultPageFactory)
    {
        $this->orderuploadLoader = $orderuploadLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Orderupload view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->orderuploadLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
