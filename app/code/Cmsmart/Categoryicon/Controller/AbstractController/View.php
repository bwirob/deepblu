<?php

namespace Cmsmart\Categoryicon\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cmsmart\Categoryicon\Controller\AbstractController\CategoryiconLoaderInterface
     */
    protected $categoryiconLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, CategoryiconLoaderInterface $categoryiconLoader, PageFactory $resultPageFactory)
    {
        $this->categoryiconLoader = $categoryiconLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Categoryicon view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->categoryiconLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
