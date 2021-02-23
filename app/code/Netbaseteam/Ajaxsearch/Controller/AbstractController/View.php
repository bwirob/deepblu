<?php

namespace Netbaseteam\Ajaxsearch\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Netbaseteam\Ajaxsearch\Controller\AbstractController\AjaxsearchLoaderInterface
     */
    protected $ajaxsearchLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, AjaxsearchLoaderInterface $ajaxsearchLoader, PageFactory $resultPageFactory)
    {
        $this->ajaxsearchLoader = $ajaxsearchLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Ajaxsearch view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->ajaxsearchLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
