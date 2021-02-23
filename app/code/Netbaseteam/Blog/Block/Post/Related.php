<?php

namespace Netbaseteam\Blog\Block\Post;


class Related extends \Netbaseteam\Blog\Block\Post
{

  public function getRelatedList(){
    $post = $this->getPost();
    $related = $post->getRelatedPost();
    $relatedList = array();
    if(!empty($related)){
      $relatedList = explode('&',$related);
    }
    return $relatedList;  
  }

  public function getRelatedCollection(){
    $relatedList = $this->getRelatedList();
    $store_id = $this->getStoreId();
    $postCollection = $this->_postFactory->create()->getCollection()
                        ->addFieldToFilter('status', array('eq'=>'1'))
                        ->addFieldToFilter('store_ids',array(
                                array('eq'=>'0'),
                                array('eq'=>$store_id)         
                            )
                        )->addFieldToFilter('post_id',array('in'=>$relatedList));

    $postCollection->setOrder('ordering','ASC');
    return $postCollection;

  }

  public function getRelatedData(){
    $relatedData = $this->getRelatedCollection()->getData();
    return $relatedData;
  }

  public function hasRelated(){
    $relatedData = $this->getRelatedData();
    if(count($relatedData)>0){
      return true;
    }
    return false;
  }

    
}
