<?php

namespace Netbaseteam\Faq\Block;

class Faqcategory extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    
    protected $_dataHelper;

    protected $_faqCategoryCollection;

    protected $_resultJsonFactory;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Netbaseteam\Faq\Helper\Data $dataHelper,
        \Netbaseteam\Faq\Model\FaqcategoryFactory $faqCategory,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->_dataHelper = $dataHelper;
        $this->_faqCategoryCollection = $faqCategory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $data);
    }

    public function getCategoryCollection(){
        $store_id = $this->getStoreId();
        $categoryCollection = $this->_faqCategoryCollection->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('store_ids',array(
                            array('eq'=>'0'),
                            array('eq'=>$store_id),
                            array('like'=>$store_id.',%'),
                            array('like'=>'%,'.$store_id),
                            array('like'=>'%,'.$store_id.',%')         
                            )
                    )
                    ->setOrder('ordering','ASC');
        return $categoryCollection;
    }

    public function getCategoryData(){
        $categoryData = $this->getCategoryCollection()->getData();
        return  $categoryData;
    }

    public function hasFaqCategory(){
        $categoryData = $this->getCategoryData();
        if(count($categoryData)==0){
            return false;
        }
        return true;
    }

    public function getCategoryIcon($icon){

        return  $this->_dataHelper->getBaseUrl().'/'.$icon;
    }

    public function getCategoryUrl(){
        $url = $this->_dataHelper->getBaseUrls().'faq/request/index';
        return $url;
    }
    

    public function getJsonPostData(array $categoryData){
        $dataPost = array(
            'action'=>$this->_dataHelper->getCategoryAction(),
            'data'=>array(
                    'faq_category_id'=>$categoryData['faq_category_id'],
                    'faq_ids'=>$categoryData['faq_ids']
                    )           
        );
        return json_encode($dataPost);
    }




    public function getStoreId(){

        return  $this->_dataHelper->getStoreviewId();
    }
   
    
}
