<?php
/*
* author: Netbase
*/
namespace Netbase\Product\Block;
class Promotion extends \Magento\Framework\View\Element\Template
{
    protected $_productCollectionFactory;
    protected $_categoryModel;
    protected $_storeManager;
    
    protected $_categoryFactory;
    protected $_category;
     
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, ///add injector
        \Magento\Catalog\Model\Category $categoryModel,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryModel = $categoryModel;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }
    public function getCurrencyData(){
      return $this->_storeManager->getStore()->getCurrentCurrencyCode(); 
      //give the currency code
      //$currencyRate = $this->_storeManager->getStore()->getCurrentCurrencyRate(); 
      //give the currency rate
    }
    public function getProductCollection($cateid)
    {
          $category = $this->_categoryModel->load($cateid);
          $collection = $this->_productCollectionFactory->create();
          $collection->addCategoryFilter($category);
          //$collection->addCategoriesFilter([1,3,4]); multi category
          $collection->addAttributeToSelect('*');
          $collection->setPageSize(3); //load only 5 products
          return $collection;
    }
    public function getCategoryUrl($categoryId) 
    {
      $this->_category = $this->_categoryFactory->create();
      $this->_category->load($categoryId);    
      return $this->_category->getName();
    }
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
                ]
            );
        }

        return $price;
    }
}
?>