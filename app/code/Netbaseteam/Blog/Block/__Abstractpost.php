<?php
/**
* Copyright © 2017 Netbaseteam. All rights reserved.
* To do: ...
*/
namespace Netbaseteam\Blog\Block;

use Magento\Framework\View\Element\Template\Context;

abstract class Abstractviewpost extends \Magento\Framework\View\Element\Template
{
    protected $_postFactory;
    protected $_coreRegistry;

    protected $_categoryFactory;

    protected $_commentFactory;
    protected $_dataHelper;
    protected $_customerSession;

    public function __construct(
        Context $context,
        \Netbaseteam\Blog\Model\PostFactory $postFactory,
        \Netbaseteam\Blog\Model\CategoryFactory $categoryFactory,
        \Netbaseteam\Blog\Model\CommentFactory $commentFactory,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        \Magento\Framework\Registry $coreRegistry
,        \Magento\Customer\Model\SessionFactory $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryFactory = $categoryFactory;
        $this->_postFactory = $postFactory;
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
}
