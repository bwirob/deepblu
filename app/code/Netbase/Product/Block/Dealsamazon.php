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


class Dealsamazon extends \Magento\Framework\View\Element\Template
{
    protected $_productCollectionFactory;
    protected $_categoryModel;
    protected $_storeManager;
    protected $_catalogProductVisibility;
    protected $_localeDate;
    protected $_date;
    protected $_resource;
    protected $_NetbaseProductHelper;
    protected $_reviewFactory;
   
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, ///add injector
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        // \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        // \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resource,
        \Netbase\Product\Helper\Data $NetbaseProductHelper,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        array $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_storeManager = $context->getStoreManager();
        $this->_date = $date;
        $this->_resource = $resource;
        $this->_NetbaseProductHelper = $NetbaseProductHelper;
        $this->_reviewFactory = $reviewFactory;
        parent::__construct($context, $data);
    }
    
    public function getStartDate(){
        $_startDateString = $this->_NetbaseProductHelper->getConfig("pro_deal/general/start_date");

        $date_arr = explode("/", $_startDateString);
        $mk = mktime(0, 0, 0, $date_arr[1], $date_arr[0], $date_arr[2]);
        $_startDate = date('Y-m-d H:i:s', $mk);     
        
        return $_startDate;
    }

    public function getEndDate(){
        $_endDateString = $this->_NetbaseProductHelper->getConfig("pro_deal/general/end_date");

        $date_arr = explode("/", $_endDateString);
        $mk = mktime(23, 59, 59, $date_arr[1], $date_arr[0], $date_arr[2]);
        $_endDate = date('Y-m-d H:i:s', $mk);
        
        return $_endDate;
    }

    public function getDealsProducts($cateId = null){
        $todayStartOfDayDate = $this->getStartDate();
        $todayEndOfDayDate = $this->getEndDate();

        $allcategoryproduct = $this->_categoryFactory->create()->load($cateId)->getProductCollection()->addAttributeToSelect('*');

        $allcategoryproduct->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
        ->addAttributeToSelect('*')
        ->addStoreFilter()
        ->addAttributeToFilter(
            'special_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
            )->addAttributeToFilter(
                'special_to_date',
                [
                    'or' => [
                        0 => ['date' => true, 'from' => $todayStartOfDayDate],
                        1 => ['is' => new \Zend_Db_Expr('not null')],
                    ]
                ],
                'left'
            )->addAttributeToFilter(
                [
                    ['attribute' => 'special_from_date', 'is' => new \Zend_Db_Expr('not null')],
                    ['attribute' => 'special_to_date', 'is' => new \Zend_Db_Expr('not null')],
                ]
            )->addAttributeToSort(
                'special_from_date',
                'desc'
            )
        ->setPageSize(6)
        ->setCurPage(isset($config['curpage'])?$config['curpage']:1)
        ->getSelect()->group("e.entity_id");

        return $allcategoryproduct;
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