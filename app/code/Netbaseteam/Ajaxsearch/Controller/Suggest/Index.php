<?php
namespace Netbaseteam\Ajaxsearch\Controller\Suggest;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
   
    protected $_resultJsonFactory;
     protected $_categoryFactory;
    protected $_helper;
    protected $_reviewFactory;

    protected $_storeManager;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
       
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory,
        
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        
        \Netbaseteam\Ajaxsearch\Helper\Data $helper,
        
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\ReviewFactory $reviewFactory
    ) {
        
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_categoryFactory = $categoryFactory;

        $this->_reviewFactory = $reviewFactory;

        $this->_helper = $helper;

        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    /**
     * Default Ajaxsearch Index page
     *
     * @return void
     */
    public function execute()
    {
        $q = $this->getRequest()->getParam('q');
        $cates = '';
        if($this->_helper->getSearchByCate()){
            $cates = $this->getRequest()->getParam('listCategories');
            $catalog_ids = explode(',',$cates);
        }
        $result = $this->_resultJsonFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
 
        $prodCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');

        $storeId = $this->_storeManager->getStore()->getId();

        $resultTotal =array();

        $_review = $this->_reviewFactory->create();

        
        /*get product by suggest name*/
        $collection01 = $prodCollection->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('url')
            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('price')
           
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('final_price');
        if($this->_helper->getSearchByCate()&&$cates){ 
            $collection01->addCategoriesFilter([array('in' => $catalog_ids)]);
        }
        $collection01->addAttributeToFilter('status', array('eq'=>'1'))
            ->addAttributeToFilter('visibility', array('neq' => '1') )
            ->addAttributeToFilter(                        
                            array(
                                array('attribute'=>'name','like' => '%'.$q.'%')

                            )

                        );

        foreach ($collection01 as $product){
             

            $resultTotal['product']['p'.$product->getID()]['name'] = $product->getName();
            /* $resultTotal['product']['p'.$product->getID()]['sku'] = $product->getSku();*/
            $resultTotal['product']['p'.$product->getID()]['small_image'] = $product->getSmallImage();
            $resultTotal['product']['p'.$product->getID()]['price'] = $product->getPrice();
            $resultTotal['product']['p'.$product->getID()]['final_price'] = $product->getFinalPrice();
            $resultTotal['product']['p'.$product->getID()]['description'] = $product->getDescription();
            $resultTotal['product']['p'.$product->getID()]['product_url'] = $product->getProductUrl();

            
            $_review->getEntitySummary($product,$storeId);

           $resultTotal['product']['p'.$product->getID()]['rating'] =  $product->getRatingSummary()->getRatingSummary();
           $resultTotal['product']['p'.$product->getID()]['review'] = $product->getRatingSummary()->getReviewsCount();
         
    }

       
         $collection02 = $prodCollection->create()
             
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('url')
            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('price')
           
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('final_price');
            
        if($this->_helper->getSearchByCate()&&$cates){
            $collection02->addCategoriesFilter(array('in' => $catalog_ids));
        }
            $collection02->addAttributeToFilter('status', array('eq'=>'1'))
            ->addAttributeToFilter('visibility', array('neq' => '1') )
            ->addAttributeToFilter(                        
                            array(
                               
                                array('attribute'=>'description','like' => '%'.$q.'%'),
                                array('attribute'=>'sku','eq' => $q)                        
                            )
                        );

    


    

    foreach ($collection02 as $product){
         
        
        $resultTotal['product']['p'.$product->getID()]['name'] = $product->getName();
       
        $resultTotal['product']['p'.$product->getID()]['small_image'] = $product->getSmallImage();
        $resultTotal['product']['p'.$product->getID()]['price'] = $product->getPrice();
        $resultTotal['product']['p'.$product->getID()]['final_price'] = $product->getFinalPrice();
        $resultTotal['product']['p'.$product->getID()]['description'] = $product->getDescription();
        $resultTotal['product']['p'.$product->getID()]['product_url'] = $product->getProductUrl();
        
        
        $_review->getEntitySummary($product,$storeId);

       $resultTotal['product']['p'.$product->getID()]['rating'] =  $product->getRatingSummary()->getRatingSummary();
       $resultTotal['product']['p'.$product->getID()]['review'] = $product->getRatingSummary()->getReviewsCount();
         
    }



    if($this->_helper->getEnableSearchCate()&&!$cates){
        $cateCollection = $this->_categoryFactory->create()->getCollection()
                ->addAttributeToSelect('Id')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('url')

                ->addFieldToFilter('is_active', array('eq'=>'1'))
                ->addAttributeToFilter(                        
                                array(
                                    array('attribute'=>'name','like' => '%'.$q.'%')                            
                                )
                            );

        foreach ($cateCollection as $cate) {

            $resultTotal['cate'][$cate->getID()]['name'] = $cate->getName();
            $resultTotal['cate'][$cate->getID()]['level'] = $cate->getLevel();
            $resultTotal['cate'][$cate->getID()]['url'] = $cate->getUrl();
            $resultTotal['cate'][$cate->getID()]['parent_cate'] = $cate->getParentCategory()->getName();        
          }

    }


    return $result->setData($resultTotal);


    }
}
