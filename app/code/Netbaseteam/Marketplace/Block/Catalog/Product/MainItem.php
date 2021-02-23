<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Catalog\Product;

class MainItem extends \Magento\Framework\View\Element\Template
{
    protected $_priceCurrency;

        /**
     * @param Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product $product,
        \Netbaseteam\Marketplace\Model\ProductFactory $productFactory,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Review\Model\Rating $rating,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_product = $product;
        $this->_productFactory = $productFactory;
        $this->_sellerdataFactory = $sellerdataFactory;
        $this->urlHelper = $urlHelper;
        $this->_rating = $rating;
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    public function getCurrentProduct() {
        return $this->_registry->registry('current_product');
    }

    public function getSellerData ($pId = null) {
        if ($pId) {
            $productId = $pId;
        } else {
            $productId = $this->getCurrentProduct()->getId();            
        }

        $mpProduct = $this->_productFactory->create()->getCollection()->addFieldToFilter('product_id', $productId);
        $sellerId = '';
        
        if (count($mpProduct)) {
            $sellerId = $mpProduct->getData()[0]['seller_id'];
        }

        $sellerDataCollection = $this->_sellerdataFactory->create()->getCollection()
            ->addFieldToFilter('seller_id',$sellerId);

        $seller=array();
        foreach($sellerDataCollection as $data){
            array_push($seller,$data->getData());
        }
        if ($seller) {
            return $seller[0];
        } else {
            return null;
        }
    }

}
