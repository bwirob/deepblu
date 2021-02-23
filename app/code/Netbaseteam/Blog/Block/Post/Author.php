<?php

namespace Netbaseteam\Blog\Block\Post;


class Author extends \Netbaseteam\Blog\Block\Post
{

  public function getAuthorInfo(){
    $post = $this->getPost();
    $authorInfo = array();
    $authorInfo['author_name'] = $post->getAuthorName();
    $authorInfo['author_email'] = $post->getAuthorEmail();
    if ($post->getAuthorAvatar()) {
    	$authorInfo['author_avatar'] = $this->_dataHelper->getPreAuthorAvatarUrl().$post->getAuthorAvatar();
    } else {
    	$authorInfo['author_avatar'] = '';
    }
    
    $authorInfo['author_description'] = $post->getAuthorDescription();
    return $authorInfo;
  }

  


     
    
}
