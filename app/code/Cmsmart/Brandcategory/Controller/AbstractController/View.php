<?php

namespace Cmsmart\Brandcategory\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cmsmart\Brandcategory\Controller\AbstractController\BrandcategoryLoaderInterface
     */
    protected $brandcategoryLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, BrandcategoryLoaderInterface $brandcategoryLoader, PageFactory $resultPageFactory)
    {
        $this->brandcategoryLoader = $brandcategoryLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Brandcategory view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->brandcategoryLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
