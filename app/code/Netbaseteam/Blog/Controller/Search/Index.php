<?php

namespace Netbaseteam\Blog\Controller\Search;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Netbaseteam\Blog\Helper\Data $blogHelper
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
        $q = $this->getRequest()->getParam('q_str');
        if(empty($q)){
            //$this->_forward('index', 'noroute', 'cms');
            return;
            
        }
        $request = array(
                'type'=>'search',
                'q'=>$q
            );
        $this->_coreRegistry->register('request', $request);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Search Posts for:"'.$q.'"'));
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
                'title' => __('Blog'),
                'link' => $this->_url->getUrl('blog')
            ]
        );
        
        
        return $resultPage;
    }
}
