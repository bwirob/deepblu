<?php

namespace Netbaseteam\Locator\Controller\Store;


use Magento\Framework\View\Result\PageFactory;



class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    protected $_coreRegistry;

    protected $_localtorFactory;
    protected $_localtorHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Netbaseteam\Locator\Model\LocatorFactory $localtorFactory,
        PageFactory $resultPageFactory,
        \Netbaseteam\Locator\Helper\Data $localtorHelper
    ) {
        
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_localtorFactory = $localtorFactory;
        $this->_localtorHelper = $localtorHelper;
        parent::__construct($context);
    }
    
   
    public function execute()
    {

        $storeId = $this->getRequest()->getParam('id');

        if(!$this->_localtorHelper->getConfigEnabled()){
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        if(empty($storeId)){
            $this->_forward('index', 'noroute', 'cms');
            return;
            
        }
        
        $store = $this->_localtorFactory->create()->load($storeId);
        $this->_coreRegistry->register('store', $store);

        $storeName = $store->getStoreName();
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
                'title' => __('Store Locator'),
                'link' => $this->_url->getUrl('locator')
            ]
        );
        $breadcrumbs->addCrumb('store-name',
            [
                'label' => __($storeName),
                'title' => __($storeName)
                
            ]
        );

        return $resultPage;
    }
}
