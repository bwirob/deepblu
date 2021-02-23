<?php

namespace Netbaseteam\Blog\Controller\Category;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	protected $_coreRegistry;

    protected $_categoryFactory;

    protected $_blogHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Netbaseteam\Blog\Model\CategoryFactory $categoryFactory,
        \Netbaseteam\Blog\Helper\Data $blogHelper,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryFactory = $categoryFactory;
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
        
        $q = $this->getRequest()->getParam('blog_category_id');
        if(empty($q)){
            $this->_forward('index', 'noroute', 'cms');
            return;
        }
        $request = array(
                'type'=>'category',
                'q'=>$q
            );
        $this->_coreRegistry->register('request', $request);

        $categoryName = $this->_categoryFactory->create()->load($q)->getName();

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set(__($categoryName));
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

        $breadcrumbs->addCrumb('category-view',
            [
                'label' => __($categoryName),
                'title' => __($categoryName)
            ]
        );
        
        
        return $resultPage;
    }
}
