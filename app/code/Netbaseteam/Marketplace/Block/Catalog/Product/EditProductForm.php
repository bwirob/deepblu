<?php

/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Catalog\Product;
use Magento\Catalog\Model\Category;

class EditProductForm extends \Magento\Framework\View\Element\Template
{
    protected $_priceCurrency;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Tax\Model\ResourceModel\TaxClass\Collection $taxClassCollection,
        \Magento\Framework\File\Size $fileConfig,
        \Magento\Eav\Model\Config $eavConfig,
        \Netbaseteam\Marketplace\Helper\Data $helper,
		Category $category,
        \Magento\Framework\Registry $registry,
        \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedProduct,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        array $data = []
    )
    {
        $this->formkey = $formKey;
        $this->_storeManager = $context->getStoreManager();
        $this->_priceCurrency = $priceCurrency;
        $this->_visibility = $visibility;
        $this->_taxClassCollection = $taxClassCollection;
        $this->_assetRepo = $context->getAssetRepository();
        $this->_fileConfig = $fileConfig;
        $this->eavConfig = $eavConfig;
        $this->_helper = $helper;
		$this->_category = $category;
        $this->_registry = $registry;
        $this->groupedProduct = $groupedProduct;
        $this->_objectManager = $objectmanager;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        return $this;
    }

    public function getFormKey()
    {
        return $this->formkey->getFormKey();
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getCurrencySymbol()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencySymbol();
    }

    public function getProductVisibility()
    {
        return $this->_visibility->getOptionArray();
    }

    public function getTaxClassCollection() {
        return $this->_taxClassCollection;
    }

    public function getSpaceImage()
    {
        return $this->_assetRepo->getUrl('images/spacer.gif');
    }

    public function getFileMaxSize()
    {
        return $this->_fileConfig->getMaxFileSize();
    }

    public function getHtml() {
        return $this->_escaper->escapeHtml('image');
    }

    public function getAllowedSets(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $coll = $objectManager->create(\Magento\Catalog\Model\Product\AttributeSet\Options::class);

        $allowed=explode(',',$this->_helper->getAllowedAttributesetIds());

        foreach($coll->toOptionArray() as $d){
            if(in_array($d['value'], $allowed)) {
                $this->_options[] = ['label' => $d['label'], 'value' => $d['value']];
            }
        }
        return $this->_options;
    }

    public function getCategory()
    {
        return $this->_category;
    }

    public function getProductModel() {
        if ($this->_registry->registry('product_edit')) {
            return $this->_registry->registry('product_edit');
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getChildGrouped()
    {
        $product = $this->getProductModel();
        $products = $product->getTypeInstance(true)->getAssociatedProducts($product);
        return $products;
    }

    /**
     * @return array
     */
    public function getChildGroupedIds()
    {
        $products = $this->getChildGrouped();
        $childIds = array();
        if (count($products)) {
            foreach ($products as $product) {
                $childIds[] = $product->getId();
            }
        }

        return $childIds;
    }

    /**
     * @return array
     */
    public function getChildGroupedQty($id)
    {
        $products = $this->getChildGrouped();
        $qty = '';
        if (count($products)) {
            foreach ($products as $product) {
                if($id == $product->getId()) $qty = $product->getQty();
            }
        }

        return $qty;
    }

    /**
     * @return array
     */
    public function getChildRelated()
    {
        $product = $this->getProductModel();
        $products = $product->getRelatedProductCollection()->addAttributeToSelect(
            'required_options'
        )->setPositionOrder()->addStoreFilter();
        return $products;
    }

    /**
     * @return array
     */
    public function getChildRelatedIds()
    {
        $products = $this->getChildRelated();
        $childIds = array();
        if (count($products)) {
            foreach ($products as $product) {
                $childIds[] = $product->getId();
            }
        }
        return $childIds;
    }

        /**
     * @return array
     */
    public function getChildUpsell()
    {
        $product = $this->getProductModel();
        $products = $product->getUpsellProductCollection()->addAttributeToSelect(
            'required_options'
        )->setPositionOrder()->addStoreFilter();
        return $products;
    }

    /**
     * @return array
     */
    public function getChildUpsellIds()
    {
        $products = $this->getChildUpsell();
        $childIds = array();
        if (count($products)) {
            foreach ($products as $product) {
                $childIds[] = $product->getId();
            }
        }
        return $childIds;
    }

        /**
     * @return array
     */
    public function getChildCrosssell()
    {
        $product = $this->getProductModel();
        $products = $product->getCrosssellProductCollection()->addAttributeToSelect(
            'required_options'
        )->setPositionOrder()->addStoreFilter();
        return $products;
    }

    /**
     * @return array
     */
    public function getChildCrosssellIds()
    {
        $products = $this->getChildCrosssell();
        $childIds = array();
        if (count($products)) {
            foreach ($products as $product) {
                $childIds[] = $product->getId();
            }
        }
        return $childIds;
    }

    public function getRootCategory(){
        $store = $this->_storeManager->getStore();
        $categoryId = $store->getRootCategoryId();
        $category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
        return $category;
    }

    public function getTreeCategory($category, $parent, $ids = array(), $checkedCat){
        $rootCategoryId = $this->getRootCategory()->getId();
        $children = $category->getChildrenCategories();
        $childrenCount = count($children);
        $htmlLi = '<li lang="'.$category->getId().'">';
        $html[] = $htmlLi;
        $ids[] = $category->getId();
        $html[] = '<a id="node'.$category->getId().'">';

        $html[] = '<input lang="'.$category->getId().'" type="checkbox" id="radio'.$category->getId().'" name="product[category_ids][]" value="'.$category->getId().'" class="checkbox'.$parent.'"';
        if(in_array($category->getId(), $checkedCat)){
            $html[] = 'checked';
        }
        $html[] = '/>';


        $html[] = '<label for="radio'.$category->getId().'">' . $category->getName() . '</label>';

        $html[] = '</a>';

        $htmlChildren = '';
        if($childrenCount>0){
            foreach ($children as $child) {
                $_child = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($child->getId());
                $htmlChildren .= $this->getTreeCategory($_child, $category->getId(), $ids, $checkedCat);
            }
        }
        if (!empty($htmlChildren)) {
            $html[] = '<ul id="container'.$category->getId().'" style="display:none">';
            $html[] = $htmlChildren;
            $html[] = '</ul>';
        }

        $html[] = '</li>';
        $html = implode("\n", $html);
        return $html;
    }
}
