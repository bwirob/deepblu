<?php
namespace Netbaseteam\Blog\Block;

class Postlist extends \Magento\Framework\View\Element\Template
{

     protected $_postFactory;

    protected $_coreRegistry;

    protected $_categoryFactory;

    protected $_commentFactory;
    protected $_dataHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Netbaseteam\Blog\Model\PostFactory $postFactory,
        \Netbaseteam\Blog\Model\CategoryFactory $categoryFactory,
        \Netbaseteam\Blog\Model\CommentFactory $commentFactory,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_dataHelper = $dataHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryFactory = $categoryFactory;
        $this->_postFactory = $postFactory;
        $this->_commentFactory = $commentFactory;
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

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getPosts()) {
            $limitPost = $this->getNumberPostPerPage();
            $setLimit = array($limitPost=>1);
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'blog.postlist.pager'
            )->setAvailableLimit($setLimit)->setShowPerPage(true)->setCollection(
                $this->getPosts()
            );
            $this->setChild('pager', $pager);
        }
        return $this;
    }


    public function getPosts()
    {
        $postCollection = $this->switchCollection();
        return $postCollection;
    }


    public function getDataRequire(){
        return $this->_coreRegistry->registry('request');
    }

    public function switchCollection(){
        $request = $this->getDataRequire();
        if(is_array($request)){
            switch ($request['type']) {
                case 'search':
                    $postCollection = $this->getPostBySearch($request['q']);
                    break;
                case 'tag':
                    $postCollection = $this->getPostByTag($request['q']);
                    break;
                case 'category':
                    $postCollection = $this->getPostByCategory($request['q']);
                    break;
                default:
                    $postCollection = $this->getPostDefault();
                    break;
            }
            return $postCollection;
        }

       return $this->getPostDefault();

    }

    public function getPostDefault(){
        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $store_id = $this->getStoreId();
        $pageSize = $this->getNumberPostPerPage();
       
        $postCollection = $this->_postFactory->create()->getCollection()
                        ->addFieldToFilter('status', array('eq'=>'1'))
                        ->addFieldToFilter('store_ids',array(
                                array('eq'=>'0'),
                                array('eq'=>$store_id)         
                            )
                        );
        $postCollection->setOrder('creation_time','DESC');
        $postCollection->setPageSize($pageSize);
        $postCollection->setCurPage($page);
        return $postCollection;
    }

    public function getPostByCategory($categoryId){

        $postIdList = $this->getPostIdsInCategory($categoryId);

        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $store_id = $this->getStoreId();
        $pageSize = $this->getNumberPostPerPage();
       
        $postCollection = $this->_postFactory->create()->getCollection()
                        ->addFieldToFilter('status', array('eq'=>'1'))
                        ->addFieldToFilter('store_ids',array(
                                array('eq'=>'0'),
                                array('eq'=>$store_id)         
                            )
                        )->addFieldToFilter('post_id',array('in'=>$postIdList));

        $postCollection->setOrder('creation_time','DESC');
        $postCollection->setPageSize($pageSize);
        $postCollection->setCurPage($page);
        return $postCollection;
    }

    public function getPostIdsInCategory($categoryId){
        $categoryId = (int)$categoryId;
        $postIds = $this->_categoryFactory->create()->load($categoryId)->getPostIds();
        $postIdList = explode('&',$postIds);
        return $postIdList;
    }


    public function getPostBySearch($q){
        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize = $this->getNumberPostPerPage();
        $store_id = $this->getStoreId();
        $postCollection = $this->_postFactory->create()->getCollection()
                        ->addFieldToFilter('status', array('eq'=>'1'))
                        ->addFieldToFilter('store_ids',array(
                                array('eq'=>'0'),
                                array('eq'=>$store_id)         
                            )
                        )
                        ->addFieldToFilter('title',array(
                            array('like'=>'%'.$q.'%')
                        ));
        $postCollection->setOrder('creation_time','DESC');
        $postCollection->setPageSize($pageSize);
        $postCollection->setCurPage($page);
        return $postCollection;
    }

    public function getPostByTag($tag){
        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize = $this->getNumberPostPerPage();
        $store_id = $this->getStoreId();
        $postCollection = $this->_postFactory->create()->getCollection()
                        ->addFieldToFilter('status', array('eq'=>'1'))
                        ->addFieldToFilter('store_ids',array(
                                array('eq'=>'0'),
                                array('eq'=>$store_id)         
                            )
                        )
                        ->addFieldToFilter('tag',array(
                            array('like'=>'%'.$tag.'%')
                        ));
        $postCollection->setOrder('creation_time','DESC');
        $postCollection->setPageSize($pageSize);
        $postCollection->setCurPage($page);
        return $postCollection;
    }

    
    public function getPostListData(){
        $postListData = $this->getPosts()->getData();
        return $postListData;
    }

    public function hasPostInlist(){
        $postListData = $this->getPostListData();
        if(count($postListData)==0){
            return false;
        }
        return true;
    }

    public function getNumberPostPerPage(){
        return $this->_dataHelper->getNumPostPerPage();
    }



    public function getPagerHtml(){
        return $this->getChildHtml('pager');
    }

    public function getNumberComments($postId){
        $commentCollection = $this->_commentFactory->create()->getCollection()
                    ->addFieldToFilter('status', array('eq'=>'1'))
                    ->addFieldToFilter('post_id',array('eq'=>$postId))
                    ->setOrder('create_time','DESC');

        return count($commentCollection);
    }   
}

