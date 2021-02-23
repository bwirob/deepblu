<?php

namespace Netbaseteam\Blog\Block\Sidebar;

class Tag extends \Magento\Framework\View\Element\Template
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
        $store_id = $this->getStoreId();
        $postCollection = $this->_postFactory->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('store_ids',array(
                            array('eq'=>'0'),
                            array('eq'=>$store_id),      
                        )
                    )
                    ->addFieldToFilter('tag',array('neq'=>''))
                    ->setOrder('ordering','ASC');
        return $postCollection;
    }

    public function getTagData(){
        $maximunTagNumber = $this->_dataHelper->getMaximunNumberTag();
        $postData = $this->getPostCollection()->getData();
        $tagNameList = array();
        foreach ($postData as $post) {
            $tagNameList =  array_merge($tagNameList, explode(',',$post['tag']));
        }
        $tagNameList = array_unique($tagNameList);
        $tagNameList = array_slice($tagNameList,0,$maximunTagNumber);
        return  $tagNameList;
    }

    public function hasTag(){
        $tagData = $this->getTagData();
        if(count($tagData)==0){
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
