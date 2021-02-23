<?php

namespace Netbaseteam\Blog\Block\Sidebar;

class Recentcomment extends \Magento\Framework\View\Element\Template
{ 
    protected $_dataHelper;
    protected $_commentFactory;
    protected $_coreRegistry;
    protected $_postFactory;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        \Netbaseteam\Blog\Model\CommentFactory $commentFactory,
        \Netbaseteam\Blog\Model\PostFactory $postFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_commentFactory = $commentFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_postFactory = $postFactory;
        parent::__construct(
            $context,
            $data
        );
    }

    public function getCommentCollection(){
        $maxCommentRecent = $this->_dataHelper->getMaxCommentRecent();
        $store_id = $this->getStoreId();
        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize = $this->getNumberPostPerPage();
        $commentCollection = $this->_commentFactory->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('store_ids',array(
                            array('eq'=>'0'),
                            array('eq'=>$store_id)         
                        )
                    )
                    ->setOrder('create_time','DESC')
                    ->setPageSize($maxCommentRecent);
        return $commentCollection;
    }

    public function getRecentCommentData(){
        $commentData = $this->getCommentCollection()->getData();
        return  $commentData;
    }

    

    public function hasRecentComment(){
        $commentData = $this->getRecentCommentData();
        if(count($commentData)==0){
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
