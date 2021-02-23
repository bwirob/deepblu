<?php

namespace Netbaseteam\Blog\Block\Sidebar;

class Category extends \Magento\Framework\View\Element\Template
{ 
    protected $_dataHelper;
    protected $_categoryFactory;
    protected $_coreRegistry;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        \Netbaseteam\Blog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct(
            $context,
            $data
        );
    }

    public function getCategoryCollection(){
        $maxSize = $this->_dataHelper->getMaxCategoryInSidebar();
        $store_id = $this->getStoreId();
        $categoryCollection = $this->_categoryFactory->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('store_ids',array(
                            array('eq'=>'0'),
                            array('eq'=>$store_id)         
                        )
                    )
                    ->setOrder('ordering','ASC')
                    ->setPageSize($maxSize);
        return $categoryCollection;
    }



    public function getCategoryData(){
        $categoryData = $this->getCategoryCollection()->getData();
        return  $categoryData;
    }


    public function hasCategory(){
        $categoryData = $this->getCategoryData();
        if(count($categoryData)==0){
            return false;
        }
        return true;
    }

    public function getStoreId(){

        return  $this->_dataHelper->getStoreviewId();
    }

    public function getListStyle()
    {   
        return $this->_coreRegistry->registry('style');
    }
}
