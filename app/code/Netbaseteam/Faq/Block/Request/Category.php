<?php

namespace Netbaseteam\Faq\Block\Request;

class Category extends \Magento\Framework\View\Element\Template
{

    
    protected $_coreRegistry = null;

    protected $_dataHelper;

    protected $_faqCollection;

    protected $_resultJsonFactory;

    protected $_faqCategoryCollection;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Netbaseteam\Faq\Helper\Data $dataHelper,
        \Netbaseteam\Faq\Model\FaqFactory $faqFactory,
        \Netbaseteam\Faq\Model\FaqcategoryFactory $faqCategoryFactory,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->_dataHelper = $dataHelper;
        $this->_faqCollection = $faqFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_faqCategoryCollection = $faqCategoryFactory;
        parent::__construct($context, $data);
    }
    public function getRequireCategoryId(){
        $categoryId = $this->_coreRegistry->registry('category_id');
        return $categoryId;
    }



    public function getCategoryCollection(){
        $store_id = $this->getStoreId();
        $categoryId = $this->getRequireCategoryId();
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
                    ->addFieldToFilter('faq_category_id', array('eq'=>$categoryId));
        return $categoryCollection;
    }

    public function getCategoryData(){
        $categoryData = $this->getCategoryCollection()->getData();
        return  $categoryData;
    }


    public function getFaqByCategory($faqIds){
        $listfaqData = array();
        if(!empty($faqIds)){
            $faqIds = explode('&',$faqIds);
            foreach ($faqIds as $faqId) {
                $faqData =  $this->loadFaqData($faqId);
                if(!empty($faqData)){
                    $listfaqData[$faqData['faq_id']] = $faqData;
                }
            }
        }
       
       return  $listfaqData;
    }


    public function loadFaqData($fadId){
        $fadId = (int)$fadId;
        $faqData = $this->_faqCollection->create()->load($fadId)->getData();
        if($faqData['status']==0){
            return '';
        }
        return $faqData;
    }

    public function getCategoryDesignData(array $categoryData){
        $defaultDesign = [
            'fontsize'=>'14',
            'text_color'=> '#3399cc',
            'background_color'=>'#fff',
            'border_width'=>'1',
            'border_color'=>'#ccc'
        ];
        $designData = array();

        $designFaq=[
            'fontsize'=>$categoryData['fontsize']?$categoryData['fontsize']:$defaultDesign['fontsize'],
            'color'=>$categoryData['text_color']?$categoryData['text_color']:$defaultDesign['text_color'],
            'background_color'=>$categoryData['background_color']?$categoryData['background_color']:$defaultDesign['background_color'],
            'border_width'=>$categoryData['border_width']?$categoryData['border_width']:$defaultDesign['border_width'],
            'border_color'=>$categoryData['border_color']?$categoryData['border_color']:$defaultDesign['border_color'],
            'active_color'=>$categoryData['active_color']?$categoryData['active_color']:$defaultDesign['text_color'],
            'active_background'=>$categoryData['active_background']?$categoryData['active_background']:$defaultDesign['background_color'],

        ];

        $designCategory=[
            'fontsize'=>$categoryData['category_fontsize']?$categoryData['category_fontsize']:'24',
            'color'=>$categoryData['category_color']?$categoryData['category_color']:$defaultDesign['text_color']
        ];
        $designData['faq'] = $designFaq;
        $designData['category'] = $designCategory;
        return $designData;
    }


    public function getJsonDesignData(array $categoryData){
        $designData = $this->getCategoryDesignData($categoryData);
        return json_encode($designData);
        
    }

    public function hasFaqInCategory(array $categoryData){
        if(empty($categoryData['faq_ids'])){
            return false;
        }
        return true;
    }

    public function countNumberFaq(){
        $faqData = $this->getFaqBySearch();
        return count($faqData);
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
