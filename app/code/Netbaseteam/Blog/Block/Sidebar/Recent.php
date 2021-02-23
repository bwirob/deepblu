<?php

namespace Netbaseteam\Blog\Block\Sidebar;

class Recent extends \Magento\Framework\View\Element\Template
{ 
    protected $_dataHelper;
    protected $_postFactory;
    protected $_coreRegistry;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        \Netbaseteam\Blog\Model\PostFactory $postFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_postFactory = $postFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $context,
            $data
        );
    }

    public function getPostCollection(){
        $maxPostRecent = $this->_dataHelper->getMaxPostRecent();
        $store_id = $this->getStoreId();
        $postCollection = $this->_postFactory->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('store_ids',array(
                            array('eq'=>'0'),
                            array('eq'=>$store_id)         
                        )
                    )
                    ->setOrder('creation_time','DESC')
                    ->setPageSize($maxPostRecent);
        return $postCollection;
    }

    public function getRecentPostData(){
        $postData = $this->getPostCollection()->getData();
        return  $postData;
    }

    public function hasRecentPost(){
        $postData = $this->getRecentPostData();
        if(count($postData)==0){
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
