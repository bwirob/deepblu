<?php

namespace Netbaseteam\Faq\Block;

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

    public function getFaqCollection(){
        $store_id = $this->getStoreId();
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
                    ->addFieldToFilter('tag',array('neq'=>''))
                    ->setOrder('ordering','ASC');
        return $faqCollection;
    }

    public function getTagNameData(){
        $maximunTagNumber = $this->_dataHelper->getMaximunNumberTag();
        $faqData = $this->getFaqCollection()->getData();
        $tagNameList = array();
        foreach ($faqData as $faq) {
            $tagNameList =  array_merge($tagNameList, explode(',',$faq['tag']));
        }
        $tagNameList = array_unique($tagNameList);
        $tagNameList = array_slice($tagNameList,0,$maximunTagNumber);
        return  $tagNameList;
    }


    public function hasTagName(){
        $faqData = $this->getTagNameData();
        if (count($faqData)==0) {
           return false;
        }
        return  true;
    }

    public function getTagNameUrl(){
        $url = $this->_dataHelper->getBaseUrls().'faq/request/index';
        return $url;
    }
    
    public function getStoreId(){

        return  $this->_dataHelper->getStoreviewId();
    }
   
    
}
