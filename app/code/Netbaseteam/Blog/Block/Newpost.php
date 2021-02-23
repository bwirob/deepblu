<?php
namespace Netbaseteam\Blog\Block;
use Magento\Framework\View\Element\Template\Context;


class Newpost extends \Magento\Framework\View\Element\Template
{
    protected $_postFactory;
    protected $_coreRegistry;

    protected $_categoryFactory;

    protected $_dataHelper;
    protected $_customerSession;

    public function __construct(
        Context $context,
        \Netbaseteam\Blog\Model\PostFactory $postFactory,
        \Netbaseteam\Blog\Model\CategoryFactory $categoryFactory,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        \Magento\Framework\Registry $coreRegistry,        
        \Magento\Customer\Model\SessionFactory $customerSession,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryFactory = $categoryFactory;
        $this->_postFactory = $postFactory;
        $this->_customerSession = $customerSession;

        parent::__construct($context,$data);
    }
   
    public function getNewPosts(){
        $store_id = $this->getStoreId();
        $postCollection = $this->_postFactory->create()->getCollection()
                        ->addFieldToFilter('status', array('eq'=>'1'))
                        ->addFieldToFilter('store_ids',array(
                                array('eq'=>'0'),
                                array('eq'=>$store_id)         
                            )
                        );
        $postCollection->setOrder('creation_time','DESC'); 
        $postCollection->setPageSize(8);
        $newPostList = [];
        foreach ($postCollection as $post) {
            $comments = $this->_postFactory->create()->load($post->getId())->getComments();

            $newPostList[] = $post->setData('num_comments',count($comments));
            
        }
        
        return $newPostList;
  
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
   
}
