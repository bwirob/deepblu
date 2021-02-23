<?php

namespace Netbaseteam\Marketplace\Block;

use Magento\Customer\Model\AccountManagement;

class LandingPage extends \Magento\Directory\Block\Data
{

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Url $customerUrl,
        \Netbaseteam\Marketplace\Model\Sellerdata $sellerData,
        \Netbaseteam\Marketplace\Model\Seller $seller,
        \Netbaseteam\Marketplace\Model\Product $mpProduct,
        \Magento\Customer\Model\Session $customerSession,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Netbaseteam\Marketplace\Model\ProductFactory $marketplaceProductFactory,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Magento\Review\Model\Rating $rating,
        array $data = []
    )
    {
        $this->imageBuilder = $imageBuilder;
        $this->_customerUrl = $customerUrl;
        $this->_moduleManager = $moduleManager;
        $this->_sellerData = $sellerData;
        $this->_seller = $seller;
        $this->_mpProduct = $mpProduct;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_objectManager = $objectManager;
        $this->_marketplaceProductFactory = $marketplaceProductFactory;
        $this->_sellerdataFactory = $sellerdataFactory;
        $this->_rating = $rating;
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data
        );
        $this->_isScopePrivate = false;
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
        parent::_prepareLayout();
    }

    public function getAllSeller() {
        $seller = $this->_seller->getCollection()->addFieldToFilter('status',1);

        $sellerIds = array();
        foreach ($seller as $item) {
            array_push($sellerIds, $item->getSellerId());
        }

        return $sellerDataCollection = $this->_sellerData->getCollection()->addFieldToFilter('seller_id',array('in' => $sellerIds));
    }

    public function getHotsellerdata() {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $hotsellerIds = explode(",",$helper->getHotsellers());
        $sellerData = array();
        if (count($hotsellerIds)) {
            foreach ($hotsellerIds as $sellerId) {
                $sellerData[] =  $this->_objectManager->create('Netbaseteam\Marketplace\Model\Sellerdata')->getCollection()->addFieldToFilter('seller_id', $sellerId)->getFirstItem();
            }
        }
        return $sellerData;
    }

    public function getProductSeller($sellerId) {
        $products = array();
        $seller = $this->_mpProduct->getCollection()->addFieldToFilter('seller_id', $sellerId);
        if(count($seller)) {
            foreach ($seller as $item) {
                $products[]= $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
            }
        }
        return $products;
    }

    /**
     * Get config
     *
     * @param string $path
     * @return string|null
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->_customerUrl->getRegisterPostUrl();
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if ($url === null) {
            $url = $this->_customerUrl->getLoginUrl();
        }
        return $url;
    }

    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if ($data === null) {
            $formData = $this->_customerSession->getCustomerFormData(true);
            $data = new \Magento\Framework\DataObject();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int)$data['region_id'];
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Retrieve customer country identifier
     *
     * @return int
     */
    public function getCountryId()
    {
        $countryId = $this->getFormData()->getCountryId();
        if ($countryId) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    /**
     * Retrieve customer region identifier
     *
     * @return mixed
     */
    public function getRegion()
    {
        if (null !== ($region = $this->getFormData()->getRegion())) {
            return $region;
        } elseif (null !== ($region = $this->getFormData()->getRegionId())) {
            return $region;
        }
        return null;
    }

    /**
     * Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled()
    {
        return $this->_moduleManager->isOutputEnabled('Magento_Newsletter');
    }

    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param \Magento\Customer\Model\Metadata\Form $form
     * @param string|null $scope
     * @return $this
     */
    public function restoreSessionData(\Magento\Customer\Model\Metadata\Form $form, $scope = null)
    {
        if ($this->getFormData()->getCustomerData()) {
            $request = $form->prepareRequest($this->getFormData()->getData());
            $data = $form->extractData($request, $scope, false);
            $form->restoreData($data);
        }

        return $this;
    }

    /**
     * Get minimum password length
     *
     * @return string
     */
    public function getMinimumPasswordLength()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Get number of password required character classes
     *
     * @return string
     */
    public function getRequiredCharacterClassesNumber()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }

    public function getSellerId() {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();

        return $isPartner;
    }

    public function currentShopID () {
        $shopID = '';
        $sellerId = '';
        $isPartner = $this->_helper->getSellerId();
        if ($isPartner) {
            $sellerId = $this->_customerSession->getCustomerId();
            $sellerData = $this->_sellerData->getCollection()->addFieldToFilter('seller_id',$sellerId);

            foreach ($sellerData as $seller) {
                $shopID = $seller->getShopId();
            }
        }
        return $shopID;
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
     public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    public function getRatingAverage($sellerId)
    { 
        $rateSummary = 0;
        $rateCount = 0;

        $productIds = array();

        $sellerProduct = $this->_marketplaceProductFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);

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

    public function getRatingCount($sellerId)
    { 
        $ratings = 0;
        $rateCount = 0;

        $productIds = array();

        $sellerProduct = $this->_marketplaceProductFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);

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

}
