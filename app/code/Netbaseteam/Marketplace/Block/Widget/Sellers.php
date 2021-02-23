<?php
 
namespace Netbaseteam\Marketplace\Block\Widget;

class Sellers extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Netbaseteam\Marketplace\Model\ResourceModel\Sellerdata\CollectionFactory $collectionFactory,
        \Netbaseteam\Marketplace\Model\ProductFactory $marketplaceProductFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->collectionFactory = $collectionFactory;
        $this->_marketplaceProductFactory = $marketplaceProductFactory;
        $this->_productFactory = $productFactory;
        $this->imageHelper = $imageHelper;
    }

    protected function _prepareLayout() {
	    if ($head = $this->getLayout()->getBlock('head')) { 
	        $head->addCss('myfile.css');
	    }
	    return parent::_prepareLayout();
	}

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/sellers.phtml');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function getSellers() {
    	$limit = $this->getData('limit');
    	$sellerIds = explode(",", $this->getData('seller_ids'));
    	$collection = $this->collectionFactory->create()->addFieldToFilter('id',['in' => $sellerIds]);
    	$collection->getSelect()->limit($limit);

    	return $collection;
    }

    public function getImageProduct($sellerId) {
    	$mpCollection = $this->_marketplaceProductFactory
            ->create()->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('seller_id', $sellerId);
        $mpCollection->getSelect()->orderRand();
    	$productId = $mpCollection->getFirstItem()->getProductId();
    	$product = $this->_productFactory->create()->load($productId);
    	return $this->imageHelper->init($product, 'product_base_image')
                    ->constrainOnly(TRUE)
                    ->keepAspectRatio(TRUE)
                    ->keepTransparency(TRUE)
                    ->keepFrame(FALSE)
                    ->getUrl();
    }
}
