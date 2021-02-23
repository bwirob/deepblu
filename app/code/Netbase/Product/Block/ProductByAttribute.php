<?php
/*
* author: Netbase
*/
namespace Netbase\Product\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Catalog\Block\Product\ListProduct;


class ProductByAttribute extends \Magento\Framework\View\Element\Template
{
  protected $_productCollectionFactory;
  protected $_categoryModel;
  protected $_storeManager;
  protected $_listProductBlock;
  protected $_reviewFactory;
  protected  $_coreRegistry;
   
  public function __construct(
    \Magento\Backend\Block\Template\Context $context,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, ///add injector
    \Magento\Catalog\Model\CategoryFactory $categoryFactory,
    \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
    \Magento\Review\Model\ReviewFactory $reviewFactory,
	\Magento\Framework\Registry $registry,
    array $data = []
  )
  {
    $this->_productCollectionFactory = $productCollectionFactory;
    $this->_categoryFactory = $categoryFactory;
    $this->_storeManager = $context->getStoreManager();
    $this->_listProductBlock = $listProductBlock;
    $this->_reviewFactory = $reviewFactory;
	$this->_coreRegistry = $registry;
	
    parent::__construct($context, $data);
  }
  public function getCurrencyCode(){
    return $this->_storeManager->getStore()->getCurrentCurrencyCode(); 
  }
  public function getCurrencySymbol(){
    return $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
  }
  public function getProductCollection($cateId, $attribute)
  {
    // $category = $this->_categoryModel->load($cateid);
    // $collection = $this->_productCollectionFactory->create();
    // $collection->addAttributeToSelect('*');
    // $collection->addCategoryFilter($category);
    // $collection->addCategoriesFilter([1,3,4]); multi category

    $allcategoryproduct = $this->_categoryFactory->create()->load($cateId)->getProductCollection()->addAttributeToSelect('*');
    $allcategoryproduct->addAttributeToFilter($attribute,1);
    $allcategoryproduct->setPageSize(10); // load only 5 products
    return $allcategoryproduct;
  }
  public function getProductPrice(\Magento\Catalog\Model\Product $product)
  {
    $priceRender = $this->getPriceRender();
    $price = '';
    if($priceRender) {
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
    return $price.'price';
  }

  public function getAddToCartPostParams($product)
  {
    return $this->_listProductBlock->getAddToCartPostParams($product);
  }

  public function getRatingSummary($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reviewFactory = $objectManager->create('Magento\Review\Model\Review');

        $storeId = $this->_storeManager->getStore(true)->getId();
        $reviewFactory->getEntitySummary($product, $storeId);

        $ratingSummary = $product->getRatingSummary()->getRatingSummary();
        return $ratingSummary;
    }
}
?>