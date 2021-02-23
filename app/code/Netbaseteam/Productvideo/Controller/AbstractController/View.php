<?php

namespace Netbaseteam\Productvideo\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Netbaseteam\Productvideo\Controller\AbstractController\ProductvideoLoaderInterface
     */
    protected $productvideoLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, ProductvideoLoaderInterface $productvideoLoader, PageFactory $resultPageFactory)
    {
        $this->productvideoLoader = $productvideoLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Productvideo view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->productvideoLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
