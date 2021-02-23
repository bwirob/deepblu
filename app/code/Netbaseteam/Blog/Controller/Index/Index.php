<?php

namespace Netbaseteam\Blog\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_blogHelper;
    protected $_coreRegistry;
   

	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Netbaseteam\Blog\Helper\Data $blogHelper,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_blogHelper = $blogHelper;
        parent::__construct($context);
    }
	
    /**
     * Default Blog Index page
     *
     * @return void
     */
    public function execute()
    {
        if(!$this->_blogHelper->getConfigEnabled()){
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();

		$this->_view->getPage()->getConfig()->getTitle()->set(__('Blog'));
        $resultPage = $this->resultPageFactory->create();
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl('')
            ]
        );
        $breadcrumbs->addCrumb('blog',
            [
                'label' => __('Blog'),
                'title' => __('Blog')
            ]
        );
        
        
        return $resultPage;
    }
}
