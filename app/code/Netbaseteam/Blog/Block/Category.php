<?php

namespace Netbaseteam\Blog\Block;
use Magento\Framework\View\Element\Template\Context;

class Category extends \Magento\Framework\View\Element\Template
{
    
    protected $_coreRegistry;

    protected $_categoryFactory;

    protected $_dataHelper;

    public function __construct(
        Context $context,
        \Netbaseteam\Blog\Model\CategoryFactory $categoryFactory,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context,$data);
    }
  
  public function getCategory(){
  	$request = $this->_coreRegistry->registry('request');
  	$categoryId =(int)$request['q'];
  	$category = $this->_categoryFactory->create()->load($categoryId);
  	return $category;
  }

  public function getCategoryDescription(){
  	$category = $this->getCategory();
  	$description = $category->getDescription();
  	if(!empty($description)){
  		return $description;
  	}
  	return false;
  }

  public function getCategoryImage(){
  	$category = $this->getCategory();
  	$image = $category->getCategoryImage();
  	if(!empty($image)){
  		$imageUrl = $this->_dataHelper->getPreCategoryImageUrl().'/'.$category->getCategoryImage();
  		return $imageUrl;
  	}
  	return false;
  }

  public function getCategoryName(){
  	$category = $this->getCategory();
  	return $category->getName();
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
