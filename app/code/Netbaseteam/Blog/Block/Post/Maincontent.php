<?php

namespace Netbaseteam\Blog\Block\Post;


class MainContent extends \Netbaseteam\Blog\Block\Post
{


  

  public function getMetaInfo(){
  	$post = $this->getPost();
  	$metaInfo = array();
  	$metaInfo['author_name'] = $post->getAuthorName();
  	$metaInfo['author_email'] = $post->getAuthorEmail();
  	if (!empty($post->getCreationTime())) {
  		$metaInfo['creation_time'] = $this->_dataHelper->getFormatDate($post->getCreationTime());
  	} else {
  		$metaInfo['creation_time'] = '';
  	}

    $metaInfo['num_comments'] = $this->getNumberComments();
    $metaInfo['tag'] = $post->getTag();
  	
  	return $metaInfo;
  }

  public function getCategoryInfo(){
  	$post = $this->getPost();
  	$postId = $post->getId();
  	$store_id = $this->getStoreId();
  	$categoryCollection = $this->_categoryFactory->create()->getCollection()
  						->addFieldToFilter('status', array('eq'=>'1'))
                        ->addFieldToFilter('store_ids',array(
                                array('eq'=>'0'),
                                array('eq'=>$store_id)         
                            )
                        )
                        ->addFieldToFilter('post_ids',array(
                                array('eq'=>$postId),
                                array('like'=>'%&'.$postId),
                                array('like'=>'%&'.$postId.'&%'),
                                array('like'=>$postId.'&%'),         
                            )
                        );
    $info = array();
    foreach ($categoryCollection as $key => $category) {
    	$info[$key]['category_name'] = $category->getName();
    	$info[$key]['identifier'] = $category->getIdentifier();
    }
    return $info;
  }

  public function getPostContent(){
  	$post = $this->getPost();
  	return $post->getContent();
  }


     
    
}
