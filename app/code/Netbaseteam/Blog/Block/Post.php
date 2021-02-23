<?php

namespace Netbaseteam\Blog\Block;

class Post extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;

    protected $_categoryFactory;

    protected $_postFactory;

    protected $_dataHelper;
    protected $_commentFactory;
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Blog\Model\CategoryFactory $categoryFactory,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        \Netbaseteam\Blog\Model\PostFactory $postFactory,
        \Magento\Framework\Registry $coreRegistry, 
        \Netbaseteam\Blog\Model\CommentFactory $commentFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryFactory = $categoryFactory;
        $this->_postFactory =   $postFactory;
        $this->_commentFactory = $commentFactory;
        $this->_customerSession = $customerSession;
    }


    public function getStoreId(){
        return  $this->_dataHelper->getStoreviewId();
    }

    public function getNumberGridConfig(){
        $listStyle =  $this->_dataHelper->getListPostStyle();
        $numGrid = 1111;
        if ($listStyle == 'grid-2' ) {
            $numGrid = 2;
        }elseif ($listStyle == 'grid-3') {
            $numGrid = 3;
        }
        return $numGrid;
    }

    public function validShortContent($string){
      $wordsreturned = $this->_dataHelper->getConfigShortContent();  
      $retval = $string;

      $string = preg_replace('/(?<=\S,)(?=\S)/', ' ', $string);
      $string = str_replace("\n", " ", $string);
      $array = explode(" ", $string);
     
      if(count($array)<=$wordsreturned){
        $retval = $string;
      }else{
        array_splice($array, $wordsreturned+1);
        $retval = implode(" ", $array)." ...";
      }
      return $retval;
    }


    public function formatDateTime($date){
      return $this->_dataHelper->getFormatDate($date); 
    }

    public function getListStyle()
    {   
        return $this->_coreRegistry->registry('style');
    }

    public function getCategoryByPostId($postId){
        $categoryCollection = $this->_categoryFactory->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('store_ids',array(
                            array('eq'=>'0'),
                            array('eq'=>$this->getStoreId())         
                        )
                    )->addFieldToFilter('post_ids',array(
                            array('eq'=>$postId),
                            array('like'=>'%&'.$postId),
                            array('like'=>$postId.'&%'),
                            array('like'=>'%&'.$postId.'&%')      
                        )
                    )->setOrder('ordering','ASC');
                    
        return count($categoryCollection) ? $categoryCollection : false;

    }
   
    public function getImageUrl(){
        $post = $this->getPost();
        if(empty($post->getImage())){
            return false;
        }
        $imageUrl =  $this->_dataHelper->getPrePostImageUrl().'/'.$post->getImage();
        return $imageUrl;
    }


    public function getPost(){
        return $this->_coreRegistry->registry('post');
    }

    public function getPostUrl(){
        $post = $this->getPost();
        return $this->_dataHelper->getPreBlogUrl().$post->getIdentifier();
    }

    public function getPostIdentifier(){
        $post = $this->getPost();
        return $post->getIdentifier();
    }

    public function getPostTitle(){
        $post = $this->getPost();
        return $post->getTitle();
    }

    public function getNumberComments(){
        $comments = $this->getPost()->getComments();
        return count($comments);
    }

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

  public function getNextPost(){
    $post = $this->getPost();
    return count($post->getNextPost()) ? $post->getNextPost() : false;
  }

  public function getPrevPost(){
    $post = $this->getPost();
    return count($post->getPrevPost()) ? $post->getPrevPost() : false;
  }

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
