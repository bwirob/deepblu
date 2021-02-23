<?php

namespace Netbaseteam\Locator\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Netbaseteam\Localtor\Controller\AbstractController\LocaltorLoaderInterface
     */
    protected $localtorLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, LocaltorLoaderInterface $localtorLoader, PageFactory $resultPageFactory)
    {
        $this->localtorLoader = $localtorLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Localtor view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->localtorLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
