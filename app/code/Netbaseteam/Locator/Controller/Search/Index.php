<?php

namespace Netbaseteam\Locator\Controller\Search;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	
    protected $resultPageFactory;
	
	protected $_coreRegistry;
    protected $_resultJsonFactory;
    protected $_localtorFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Netbaseteam\Locator\Model\LocatorFactory $localtorFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_localtorFactory = $localtorFactory;
        parent::__construct($context);
    }
	
    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        $q = $this->getRequest()->getPostValue();
        if(!empty($q)){
            $localtorFactory = $this->_localtorFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('status', array('eq'=>'1'));
            if($q['store-name']){
                $localtorFactory->addFieldToFilter('store_name',array('like'=>'%'.$q['store-name'].'%'));
                        
            }
            if($q['country']){
                $localtorFactory->addFieldToFilter('country',array('eq'=>$q['country']));
                        
            }
            if($q['city']){
                $localtorFactory->addFieldToFilter('city',array('like'=>'%'.$q['city'].'%'));
                        
            }
            if($q['state']){
                $localtorFactory->addFieldToFilter('state',array('like'=>'%'.$q['state'].'%'));          
            }

            if($q['zip-code']){
                $localtorFactory->addFieldToFilter('zip_code',array('eq'=>$q['zip-code']));          
            }
        }else{
            $error = array('error'=>1);
            return $result->setData($error);
        }

        $storeData = array();
        foreach ($localtorFactory as $store) {
            $storeData[] =(int)$store->getLocaltorId();
        }
        return $result->setData($storeData);
        
       
    }
}
