<?php

namespace Netbaseteam\Blog\Block\Post;


class Prev extends \Netbaseteam\Blog\Block\Post
{
  public function getNextPost(){
    $post = $this->getPost();
    return count($post->getNextPost()) ? $post->getNextPost() : false;
  }

  public function getPrevPost(){
    $post = $this->getPost();
    return count($post->getPrevPost()) ? $post->getPrevPost() : false;
  }
    
    
}
