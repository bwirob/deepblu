<?php

namespace Netbaseteam\Faq\Block;

class Sidebarfaq extends \Magento\Framework\View\Element\Template
{

    
    protected $_coreRegistry = null;

    protected $_dataHelper;

    protected $_faqCollection;

    protected $_resultJsonFactory;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Netbaseteam\Faq\Helper\Data $dataHelper,
        \Netbaseteam\Faq\Model\FaqFactory $faqFactory,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->_dataHelper = $dataHelper;
        $this->_faqCollection = $faqFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $data);
    }

    public function getSideFaqCollection(){
        $numberFaqConfig = $this->_dataHelper->getNumberFaqInSidebar();
        $store_id = $this->getStoreId();
        $faqCollection = $this->_faqCollection->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('sidebar_faq', array('eq'=>'1'))
                    ->addFieldToFilter('store_ids',array(
                            array('eq'=>'0'),
                            array('eq'=>$store_id),
                            array('like'=>$store_id.',%'),
                            array('like'=>'%,'.$store_id),
                            array('like'=>'%,'.$store_id.',%')         
                            )
                    )
                    ->setOrder('ordering','ASC')
                    ->setPageSize($numberFaqConfig);
        return $faqCollection;
    }

    public function getSidebarFaqData(){
        $faqData = $this->getSideFaqCollection()->getData();
        return  $faqData;
    }

    public function hasSidebarFaq(){
        $faqData = $this->getSidebarFaqData();
        if(count($faqData)==0){
            return false;
        }
        return true;
    }

    
    public function getStoreId(){

        return  $this->_dataHelper->getStoreviewId();
    }

    public function getFormatDate($faq){
        $formatDate= '';
        if(!empty($faq['created_time'])){
            $formatDate = date("F j, Y", strtotime($faq['created_time']));
        }
        
        return $formatDate;
    }
   
    
}
