<?php

namespace Cmsmart\Megamenu\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cmsmart\Megamenu\Controller\AbstractController\MegamenuLoaderInterface
     */
    protected $megamenuLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, MegamenuLoaderInterface $megamenuLoader, PageFactory $resultPageFactory)
    {
        $this->megamenuLoader = $megamenuLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Megamenu view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->megamenuLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
