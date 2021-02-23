<?php

namespace Netbaseteam\Blog\Block\Post;


class Comment extends \Netbaseteam\Blog\Block\Post
{

    public function getCommentCollection(){
        $storeId = $this->getStoreId();
        $postId = $this->getPostId();
        $commentCollection = $this->_commentFactory->create()->getCollection()
                        ->addFieldToFilter('status', array('eq'=>'1'))
                        ->addFieldToFilter('store_ids',array(
                                array('eq'=>'0'),
                                array('eq'=>$storeId)         
                            )
                        )->addFieldToFilter('post_id',array('eq'=>$postId))
                        ->setOrder('create_time','DESC');
        return $commentCollection;
    }

    public function getEnableComment(){
        $post = $this->getPost();
        return $post->getEnableComment();
    }

    public function getCommentData(){
        $commentData = $this->getCommentCollection()->getData();
        return $commentData;
    }

    public function countNumComment(){
        $commentData = $this->getCommentData();
        return count($commentData); 
    }

    public function checkCustomerLogin(){
       $login =  $this->_customerSession->create()->isLoggedIn();
       return $login;
    } 

    public function getCustomerLoginInfo(){
        if($this->checkCustomerLogin()){
            $info = array();
            $customer = $this->_customerSession->create()->getCustomer();
            $info['cutomer_id'] =  $customer->getId();  
            $info['cutomer_name'] = $customer->getName();  
            $info['cutomer_email'] =  $customer->getEmail(); 
            return $info;
        }
        return false;
    }

    public function getFormCommentUrl(){
        return $this->_dataHelper->getPreBlogUrl().'/comment/index';
    }

    public function showAuthorInfoBox(){
        $enableConfig = $this->_dataHelper->getEnableCommentByAccount();
        $checkLogin = $this->checkCustomerLogin();
        if($enableConfig&&$checkLogin){
            return false;
        }
        return true;
    }

     public function getPostId(){
        $postId = $this->getPost()->getId();
        return $postId;
    }  

    
}
