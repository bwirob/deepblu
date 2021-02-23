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


class Bestseller extends \Magento\Framework\View\Element\Template
{
  protected $_productCollectionFactory;
  protected $_categoryModel;
  protected $_storeManager;
  protected $_catalogProductVisibility;
  protected $_resource;
  protected $_reviewFactory;
  protected  $_coreRegistry;

  public function __construct(
      \Magento\Backend\Block\Template\Context $context,
      \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, ///add injector
      \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
      \Magento\Catalog\Model\CategoryFactory $categoryFactory,
      \Magento\Framework\App\ResourceConnection $resource,
      // \Magento\Store\Model\StoreManagerInterface $storeManager,
      \Magento\Review\Model\ReviewFactory $reviewFactory,
      \Magento\Framework\Registry $registry,
      array $data = []
  )
  {
      $this->_reviewFactory = $reviewFactory;
      $this->_productCollectionFactory = $productCollectionFactory;
      $this->_catalogProductVisibility = $catalogProductVisibility;
      $this->_categoryFactory = $categoryFactory;
      $this->_storeManager = $context->getStoreManager();
      $this->_resource = $resource;
      $this->_coreRegistry = $registry;

      parent::__construct($context, $data);
  }
  public function getCurrencyData(){
    return $this->_storeManager->getStore()->getCurrentCurrencyCode(); // give the currency code
    //$currencyRate = $this->_storeManager->getStore()->getCurrentCurrencyRate(); // give the currency rate
  }
  public function getProductCollection($cateId = null)
  {
    $storeId = $this->_storeManager->getStore(true)->getId();

    $allcategoryproduct = $this->_categoryFactory->create()->load($cateId)->getProductCollection()->addAttributeToSelect('*');
    $allcategoryproduct
        /*->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())*/
        ->addAttributeToSelect('*')
        ->addStoreFilter()
        ->joinField(
            'qty_ordered',
            $this->_resource->getTableName('sales_bestsellers_aggregated_monthly'),
            'qty_ordered',
            'product_id=entity_id',
          /*   'at_qty_ordered.store_id=' . (int)$storeId, */
            'at_qty_ordered.qty_ordered > 0',
            'left'
        )
        ->setPageSize(10)
        ->setCurPage(1)
        ->getSelect()->group("e.entity_id");
      return $allcategoryproduct;
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