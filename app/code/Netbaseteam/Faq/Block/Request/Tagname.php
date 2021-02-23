<?php

namespace Netbaseteam\Faq\Block\Request;

class Tagname extends \Magento\Framework\View\Element\Template
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
     public function getRequireTag(){
        $tag = $this->_coreRegistry->registry('tag');
        return $tag;
    }


    public function getFaqCollection(){
        $store_id = $this->getStoreId();
        $tag = $this->getRequireTag();
        $faqCollection = $this->_faqCollection->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('store_ids',array(
                            array('eq'=>'0'),
                            array('eq'=>$store_id),
                            array('like'=>$store_id.',%'),
                            array('like'=>'%,'.$store_id),
                            array('like'=>'%,'.$store_id.',%')         
                            )
                    )
                    ->addFieldToFilter('tag',array(
                            array('eq'=>$tag),
                            array('like'=>$tag.',%'),
                            array('like'=>'%,'.$tag),
                            array('like'=>'%,'.$tag.',%')
                    ))
                    ->setOrder('ordering','ASC');
        return $faqCollection;
    }

    public function getFaqByTagName(){
        $faqData = $this->getFaqCollection()->getData();
        return  $faqData;
    }


    public function hasFaqByTagName(){
        $faqData = $this->getFaqByTagName();
        if (count($faqData)==0) {
           return false;
        }
        return  true;
    }

    public function getFormatDate($faq){
        $formatDate= '';
        if(!empty($faq['created_time'])){
            $formatDate = date("F j, Y", strtotime($faq['created_time']));
        }
        
        return $formatDate;
    }


    
    public function getStoreId(){

        return  $this->_dataHelper->getStoreviewId();
    }
   
    
}
