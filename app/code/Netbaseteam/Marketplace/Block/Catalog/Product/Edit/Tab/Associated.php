<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Edit\Tab;

class Associated extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Netbaseteam\Marketplace\Model\Config\Source\Attributesetid $attributesetid,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->request = $request;
        $this->productFactory = $productFactory;
        $this->imageHelper = $imageHelper;
        $this->attributesetid = $attributesetid;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    public function getProduct($id) {
        return $this->productFactory->create()->load($id);
    }

    public function getThumbnail($product) {
        $imageHelper = $this->imageHelper->init($product, 'product_page_image_small')->resize(55);
        $src = $imageHelper->getUrl();
        $alt = $this->getAlt($product) ?: $imageHelper->getLabel();

        $imageHtml = "<img alt='$alt' src='$src' class='admin__control-thumbnail' />";
        return $imageHtml;
    }

    public function getAttributeSet($value) {
        $attributes = $this->attributesetid->getAllOptions();
        foreach ($attributes as $attribute) {
            if ($value == $attribute['value']) {
                return $attribute['label'];
            }
        }
    }

    public function getStatusLabel($value) {
        if($value == 1) {
            return 'Enable';
        }
        return 'Disable';
    }

        /**
     * @return array
     */
    public function getChildRelated()
    {
        $product = $this->_registry->registry('product_edit');
        $products = array();
        if ($product) {
            $products = $product->getRelatedProducts();
        }
        return $products;
    }

    /**
     * @return array
     */
    public function getChildUpsell()
    {
        $product = $this->_registry->registry('product_edit');
        $products = array();
        if ($product) {
            $products = $product->getUpSellProducts();
        }
        return $products;
    }

            /**
     * @return array
     */
    public function getChildCrosssell()
    {
        $product = $this->_registry->registry('product_edit');
        $products = array();
        if ($product) {
            $products = $product->getCrossSellProducts();
        }
        return $products;
    }
}
