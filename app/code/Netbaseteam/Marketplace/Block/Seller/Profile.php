<?php

namespace Netbaseteam\Marketplace\Block\Seller;

use Magento\Catalog\Block\Product\AbstractProduct;

class Profile extends AbstractProduct
{

    /**
     * @var \Netbaseteam\Marketplace\Helper\Data
     */
    protected $_helper;

    /**
     * Profile constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Netbaseteam\Marketplace\Model\Sellerdata $sellerData
     * @param \Netbaseteam\Marketplace\Model\Product $sellerProduct
     * @param \Netbaseteam\Marketplace\Model\ProductFactory $marketplaceProductFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Review\Model\Rating $rating
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Netbaseteam\Marketplace\Helper\Data $helper
     * @param \Magento\Eav\Model\Entity\Attribute $entityAttribute
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory
     * @param \Netbaseteam\Marketplace\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Netbaseteam\Marketplace\Model\Sellerdata $sellerData,
        \Netbaseteam\Marketplace\Model\Product $sellerProduct,
        \Netbaseteam\Marketplace\Model\ProductFactory $marketplaceProductFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Review\Model\Rating $rating,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        array $data = []
    )
    {
        $this->_sellerData = $sellerData;
        $this->_sellerProduct = $sellerProduct;
        $this->_marketplaceProductFactory = $marketplaceProductFactory;
        $this->_productFactory = $productFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_rating = $rating;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_entityAttribute = $entityAttribute;
        $this->_attributeFactory = $attributeFactory;
        parent::__construct($context, $data);
    }

    /**
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->setKeywords($this->getShopData()['meta_keyword']);
        $this->pageConfig->setDescription($this->getShopData()['meta_description']);
        parent::_prepareLayout();
        $moduleName = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $action     = $this->_request->getActionName();
        if ($moduleName == 'customer' && $controller == 'account' &&  $action == 'index' && $this->_helper->releaseLimit()) {
            $this->setTemplate('Netbaseteam_Marketplace::account/navigation.phtml');
        }
    }

    public function getFacebookButton()
    {
        $url = 'https://www.facebook.com/'.$this->getShopData()['facebook_id'].'';
        $facebookID = 100000931746268;
        if ($this->getShopData()['facebook_id']) {
            return '<div class="facebook_button social-button"><div id="fb-root"></div>
                <script>
                    (function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s); js.id = id;
                        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=' . $facebookID . '";
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, \'script\', \'facebook-jssdk\'));
                </script>                
                <div class="fb-like" data-layout="button_count" data-width="400" data-show-faces="false"  data-href="' . $url . '"  data-send="false"></div>
                <div class="fb-share-button" data-href="' . $url . '"data-layout="button_count"></div>
                </div>';
        } else {
            return '';
        }

    }

    public function getFacebookPlugin()
    {
        if (!empty($this->getShopData()['facebook_id'])) {
            $url = 'https://www.facebook.com/'.$this->getShopData()['facebook_id'].'';
        } else {
            return '';
        }
        $facebookID = 100000931746268;

        return '<div class="facebook_plugin_page">
 <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=' . $facebookID . '";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>

  <div class="fb-page" data-href="' . $url . '" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/facebook" class="fb-xfbml-parse-ignore"><a href="' . $url . '">Facebook</a></blockquote></div>
  </div>';
    }

    public function getTwitterButton()
    {
        $url = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);

        if ($this->getShopData()['twitter_id']) {
            return "<div class='twitter_button social-button'>
  <a href='https://twitter.com/share' class='twitter-share-button' data-url='" . $url . "' >Tweet</a>
  <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div>";
        } else {
            return '';
        }

    }


    public function getGooglePlusButton()
    {
        if ($this->getShopData()['twitter_id']) {
            return '<div class="google_button social-button">
  <div class="g-plusone" data-size="medium"  data-annotation="bubble"></div>
  </div>
  <script src="https://apis.google.com/js/platform.js" async defer></script>';
        } else {
            return '';
        }

    }

    public function getShopData()
    {
        $shopID = $this->getRequest()->getParam('shop');
        $sellerDataCollection = $this->_sellerData->getCollection()->addFieldToFilter('shop_id',$shopID);

        $sellerData=array();
        foreach($sellerDataCollection as $data){
            array_push($sellerData,$data->getData());
        }
        if ($sellerData) {
            return $sellerData[0];
        } else {
            return null;
        }
    }

    public function currentShopID () {
        $shopID = '';
        $sellerId = $this->_helper->getSellerId();
        if ($sellerId) {
            $sellerData = $this->_sellerData->getCollection()->addFieldToFilter('seller_id',$sellerId);

            foreach ($sellerData as $seller) {
                $shopID = $seller->getShopId();
            }
        }
        return $shopID;
    }

    public function getCustomer() {
        return $this->_customerSession->getCustomer();
    }

    public function getRatingAverage()
    { 
        $shopId = $this->getRequest()->getParam('shop');
        $sellerData = $this->_sellerData->getCollection()->addFieldToFilter('shop_id',$shopId);
        
        $sellerId = '';
        foreach ($sellerData as $seller) {
            $sellerId = $seller->getSellerId();
        }

        $rateSummary = 0;
        $rateCount = 0;

        $productIds = array();
        $sellerProduct = $this->_sellerProduct->getCollection()->addFieldToFilter('seller_id', $sellerId);

        foreach ($sellerProduct as $item) {
            $productIds[]  = $item->getProductId();
        }

        if(count($productIds)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeId = $this->_storeManager->getStore()->getId();
            foreach($productIds as $productId) {
                $_product=$objectManager->create("Magento\Catalog\Model\Product")->load($productId);
                $_review=$objectManager->create("Magento\Review\Model\Review");
                $_reviewFact=$objectManager->get("Magento\Review\Model\ReviewFactory");
                $_reviewFact->create()->getEntitySummary($_product, $storeId);
                if($_product->getRatingSummary()->getRatingSummary()) {
                    $rateSummary += $_product->getRatingSummary()->getRatingSummary();
                    $rateCount++;
                }  
            }
        } 

        if ($rateCount) {
            return $rateSummary/$rateCount;
        }

        return 0;
    }

    public function getRatingCount()
    { 
        $shopId = $this->getRequest()->getParam('shop');
        $sellerData = $this->_sellerData->getCollection()->addFieldToFilter('shop_id',$shopId);
        
        $sellerId = '';
        foreach ($sellerData as $seller) {
            $sellerId = $seller->getSellerId();
        }

        $rateCount = 0;

        $productIds = array();
        $sellerProduct = $this->_sellerProduct->getCollection()->addFieldToFilter('seller_id', $sellerId);

        foreach ($sellerProduct as $item) {
            $productIds[]  = $item->getProductId();
        }

        if(count($productIds)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeId = $this->_storeManager->getStore()->getId();
            foreach($productIds as $productId) {
                $_product=$objectManager->create("Magento\Catalog\Model\Product")->load($productId);
                $_review=$objectManager->create("Magento\Review\Model\Review");
                $_reviewFact=$objectManager->get("Magento\Review\Model\ReviewFactory");
                $_reviewFact->create()->getEntitySummary($_product, $storeId);  
                $rateCount += $_product->getRatingSummary()->getReviewsCount();
            }
        }

        return $rateCount;
    }

    public function getCategoryCollection()
    {
        $products = $this->getProductCollection();
        $categoryCollection = array(); 
        $groupProCats = array();       
        foreach($products as $item)
        {      
            $product = $this->_productFactory->create()->load($item->getId());
            $proCats = $product->getCategoryIds();
            $groupProCats = array_merge($groupProCats, $proCats);
        }
        $finalProCats = array_unique($groupProCats);
        if (count($finalProCats)) {
            foreach ($finalProCats as $cat) {
                $categoryCollection[] = $this->_categoryFactory->create()->load($cat);                        
            } 
        }
        return $categoryCollection;
    }

    public function getCategoryProduct($categoryId = 0)
    {
        $categoryCollection = $this->getCategoryCollection();

        $catIds = array();
        foreach ($categoryCollection as $cat) {
            $catIds[] = $cat->getId();
        }

        $pIdArr = array();
        if ($categoryId && in_array($categoryId, $catIds)) {
            $products = $this->_categoryFactory->create()->load($categoryId)->getProductCollection()->addAttributeToSelect('*');            
            
            $pIds = array();
            if (count($products)) {
                foreach ($products as $product) {
                    $pIds[] = $product->getId();
                }
            }

            $mpIds = array();
            $mpProducts = $this->getProductCollection();
            if (count($mpProducts)) {
                foreach ($mpProducts as $product) {
                    $mpIds[] = $product->getId();
                }
            }

            $pIdArr = array_intersect($pIds, $mpIds);
        }
        return count($pIdArr);
    }

    public function getAttributeFilter() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $filterableAttributes = $objectManager->get(\Magento\Catalog\Model\Layer\Category\FilterableAttributeList::class);
        $attributes = $filterableAttributes->getList()
        ->addFieldToFilter('is_filterable', 1)
        ->addFieldToFilter('is_visible', 1)
        ->addFieldToFilter('used_in_product_listing', 1);

        $products = $this->getProductCollection();
        $attributeCode = array();
        $attributeFilter = array();

        if (count($products)) {
            foreach ($products as $product) {
                $mpAttributes = $product->getAttributes();
                foreach ($mpAttributes as $attribute) { 
                    $attributeCode[] = $attribute->getAttributeCode();
                }
            }
        }
        
        if (count($attributes)) {
            foreach ($attributes as $item) {
                $itemCode = $item->getAttributeCode();
                if(in_array($itemCode, $attributeCode)) {
                    $attributeFilter[] = $item;
                }
            }
        }
        return $attributeFilter;
    }

    public function getExistingOptions($code = '')
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $eavConfig = $objectManager->get('\Magento\Eav\Model\Config');
        $attribute = $eavConfig->getAttribute('catalog_product', $code);
        $options = $attribute->getSource()->getAllOptions();
        
        return $options;
    }

    public function getExistingOptionsValue($code,$value)
    {
        $existingOption = $this->getExistingOptions($code);
        foreach ($existingOption as $option) {
            if($value == $option['value']) {
                return $option['label'];
            }
        }
        return '';
    }

    public function getAttributeInfo($attributeCode)
    {
        return $this->_entityAttribute
                    ->loadByCode('catalog_product', $attributeCode);
    }

    public function getCategoryInfo($catId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category = $objectManager->create('Magento\Catalog\Model\Category')->load($catId);

        return $category;
    }

    public function getProductCollection($attrCode = null, $attrVal = null) 
    {
        $shopId = $this->getRequest()->getParam('shop');
        $categoryId = (int)$this->getRequest()->getParam('cat');
        $priceFilter = $this->getRequest()->getParam('price');
        $productName = $this->getRequest()->getParam('product_name');  
        
        $sellerData = $this->_sellerData->getCollection()->addFieldToFilter('shop_id',$shopId);
        
        $sellerID = '';
        foreach ($sellerData as $seller) {
            $sellerID = $seller->getSellerId();
        }

        $mpCollection = $this->_marketplaceProductFactory
            ->create()->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('seller_id', $sellerID);

        $products=array();
        foreach($mpCollection as $data){
            array_push($products,$data->getProductId());
        }


        $collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id',array('in'=>$products));
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToFilter('status', array('eq'=>'1'))
            ->addAttributeToFilter('visibility', array('neq' => '1'));

        if($productName) {
            $collection->addAttributeToFilter('name', array('like'=>'%'.$productName.'%'));
        }

        if ($categoryId) {
            $collection->addCategoriesFilter(['in' => $categoryId]);
        }
        
        if($this->getRequest()->getParam('product')) {
            if ($priceFilter) {
                $priceReq = explode("-",$priceFilter);
                $minPrice = $priceReq[0];
                $maxPrice = $priceReq[1];
    
                $collection->addAttributeToFilter('price', array(
                        array('from' => $minPrice, 'to' => $maxPrice),
                    )
                );
            }
    
            $allRequest = $this->getRequest()->getParams();
            unset($allRequest['price']);
            
            $attributeCodes = $this->getProductAttributes();

            if (count($allRequest)) {
                foreach ($allRequest as $code=>$value) {
                    if (in_array($code, $attributeCodes)) {
                        $collection->addAttributeToSelect('*')
                        ->addAttributeToFilter($code, 
                        array('eq' => $value)
                      );
                    }
                }
            }
            if ($attrCode) {
                $collection->addAttributeToSelect('*')
                ->addAttributeToFilter($attrCode, 
                array('neq' => '')
              );
            }
            if ($attrCode && $attrVal) {
                $collection->addAttributeToSelect('*')
                ->addAttributeToFilter($attrCode, 
                array('eq' => $attrVal)
              );
            }
        }

        return $collection;
    }

    public function getProductAttributes() {
        $attributeInfo = $this->_attributeFactory->getCollection();
        $attributeCodes = array();
        foreach($attributeInfo as $attributes)
        {
            $attributeCodes[] = $attributes->getAttributeCode();
        }

        return $attributeCodes;
    }

}
