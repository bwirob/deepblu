<?php

namespace Netbaseteam\Faq\Block\Product;

class Faqlist extends \Magento\Framework\View\Element\Template
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

    public function getCurentProduct(){
        $product = $this->_coreRegistry->registry('current_product');
        return $product;
    }

    public function getFaqCollection(){
        $store_id = $this->getStoreId();
        $maxNumberfaq = $this->_dataHelper->getMaxFaqPerProductPage();
        $productId = $this->getCurentProduct()->getId();
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
                    ->addFieldToFilter('product_ids',array(
                            array('eq'=>$productId),
                            array('like'=>$productId.'&%'),
                            array('like'=>'%&'.$productId),
                            array('like'=>'%&'.$productId.'&%')
                    ))
                    ->setOrder('ordering','ASC')
                    ->setPageSize($maxNumberfaq);
        return $faqCollection;
    }

    public function getFaqByProductView(){
        $faqData = $this->getFaqCollection()->getData();
        return  $faqData;
    }

    public function hasFaqByProductView(){
        $faqData = $this->getFaqByProductView();
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
