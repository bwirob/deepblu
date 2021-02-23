<?php

namespace Netbaseteam\Blog\Controller\Post;

use Magento\Framework\View\Result\PageFactory;



class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    protected $_coreRegistry;

    protected $_postFactory;

    protected $_blogHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Netbaseteam\Blog\Model\PostFactory $postFactory,
        \Netbaseteam\Blog\Helper\Data $blogHelper,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_postFactory = $postFactory;
        $this->_blogHelper = $blogHelper;
        parent::__construct($context);
    }
    
   
    public function execute()
    {

        if(!$this->_blogHelper->getConfigEnabled()){
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        $postId = $this->getRequest()->getParam('post_id');

        if(empty($postId)){
            $this->_forward('index', 'noroute', 'cms');
            return;
            
        }

        
        $post = $this->_postFactory->create()->load($postId);
        $this->_coreRegistry->register('post', $post);
        $postTitle = $post->getTitle();
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set(__($postTitle));
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
        $breadcrumbs->addCrumb('post-name',
            [
                'label' => __($postTitle),
                'title' => __($postTitle)
                
            ]
        );

        return $resultPage;
    }
}
