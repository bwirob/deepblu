<?php

namespace Netbaseteam\Blog\Block\Post;


class Metabutton extends \Netbaseteam\Blog\Block\Post
{

  public function getTagList(){
  	$post = $this->getPost();
  	$tagInfo = $post->getTag();
    if(!empty($tagInfo)){
      $tag = explode(',',$tagInfo);
      return $tag;
    }
  	return false;
  }

  public function getPreTagUrl(){
    return $this->_dataHelper->getPreBlogUrl().'/tag/index?tag=';
  }

  


     
    
}
