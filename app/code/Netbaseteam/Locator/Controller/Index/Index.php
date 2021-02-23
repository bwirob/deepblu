<?php

namespace Netbaseteam\Locator\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $_localtorHelper;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Netbaseteam\Locator\Helper\Data $localtorHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_localtorHelper = $localtorHelper;
        parent::__construct($context);
    }
	
    /**
     * Default Localtor Index page
     *
     * @return void
     */
    public function execute()
    {
       if(!$this->_localtorHelper->getConfigEnabled()){
            $this->_forward('index', 'noroute', 'cms');
            return;
        }
        

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $resultPage = $this->resultPageFactory->create();
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl('')
            ]
        );
        $breadcrumbs->addCrumb('locator',
            [
                'label' => __('Store Locator'),
                'title' => __('Store Locator')
            ]
        );
        
        
        return $resultPage;
    
    }
}
