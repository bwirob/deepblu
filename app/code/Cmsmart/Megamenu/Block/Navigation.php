<?php

namespace Cmsmart\Megamenu\Block;

/**
 * Megamenu content block
 */
class Navigation extends \Magento\Framework\View\Element\Template
{
    /**
     * Megamenu collection
     *
     * @var Cmsmart\Megamenu\Model\ResourceModel\Megamenu\Collection
     */
    protected $_megamenuCollection = null;

    /**
     * Megamenu factory
     *
     * @var \Cmsmart\Megamenu\Model\MegamenuFactory
     */
    protected $_megamenuCollectionFactory;

    /** @var \Cmsmart\Megamenu\Helper\Data */
    protected $_dataHelper;

    protected $_categoryHelper;
    protected $_productFactory;
    protected $_objectManagerr;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cmsmart\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $megamenuCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cmsmart\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $megamenuCollectionFactory,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Cmsmart\Megamenu\Helper\Data $dataHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_megamenuCollectionFactory = $megamenuCollectionFactory;
        $this->_dataHelper = $dataHelper;
        $this->_categoryHelper = $categoryHelper;
        $this->_productFactory = $productFactory;
        $this->_objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Retrieve megamenu collection
     *
     * @return Cmsmart\Megamenu\Model\ResourceModel\Megamenu\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_megamenuCollectionFactory->create();
        return $collection;
    }

    /**
     * Retrieve prepared megamenu collection
     *
     * @return Cmsmart\Megamenu\Model\ResourceModel\Megamenu\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_megamenuCollection)) {
            $this->_megamenuCollection = $this->_getCollection();
        }
        return $this->_megamenuCollection;
    }

    public function _getTopLabel($cat_id, $pid, $lbl_type)
    {
        $menu = $this->getCollection();
        foreach ($menu as $m) {
            if ($m->getCategoryId() == $cat_id) {
                if ($lbl_type == \Cmsmart\Megamenu\Model\Label::lblHot) {
                    $p_ids = explode(",", $m->getTopHotProducts());
                } elseif ($lbl_type == \Cmsmart\Megamenu\Model\Label::lblNew) {
                    $p_ids = explode(",", $m->getTopNewProducts());
                } elseif ($lbl_type == \Cmsmart\Megamenu\Model\Label::lblSale) {
                    $p_ids = explode(",", $m->getTopSaleProducts());
                }
                break;
            }
        }
        if (in_array($pid, $p_ids)) {
            return true;
        }
        return;
    }

    public function _getVerLabel($cat_id, $pid, $lbl_type)
    {
        $menu = $this->getCollection();
        foreach ($menu as $m) {
            if ($m->getCategoryId() == $cat_id) {
                if ($lbl_type == \Cmsmart\Megamenu\Model\Label::lblHot) {
                    $p_ids = explode(",", $m->getLeftHotProducts());
                } elseif ($lbl_type == \Cmsmart\Megamenu\Model\Label::lblNew) {
                    $p_ids = explode(",", $m->getLeftNewProducts());
                } elseif ($lbl_type == \Cmsmart\Megamenu\Model\Label::lblSale) {
                    $p_ids = explode(",", $m->getLeftSaleProducts());
                }
                break;
            }
        }
        if (in_array($pid, $p_ids)) {
            return true;
        }
        return;
    }

    public function getMenuField($field_name, $cat_id)
    {
        $mnColection = $this->getCollection();

        $value = "";
        foreach ($mnColection as $menu) {
            if ($menu->getCategoryId() == $cat_id) {
                switch ($field_name) {
                    case \Cmsmart\Megamenu\Model\Fields::top_label:
                        $value = $menu->getTopLabel();
                        if ($value == "new") {
                            $value = $this->getViewFileUrl('Cmsmart_Megamenu/images/icon_new.png');
                        }
                        if ($value == "hot") {
                            $value = $this->getViewFileUrl('Cmsmart_Megamenu/images/icon_hot.png');
                        }
                        if ($value == "sale") {
                            $value = $this->getViewFileUrl('Cmsmart_Megamenu/images/icon_sale.png');
                        }
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_block_top:
                        $value = $menu->getTopBlockTop();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_block_bottom:
                        $value = $menu->getTopBlockBottom();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_block_left:
                        $value = $menu->getTopBlockLeft();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_block_right:
                        $value = $menu->getTopBlockRight();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_sku:
                        $value = $menu->getTopSku();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_label_container:
                        $value = $menu->getTopLabelContainer();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_left_block_sku:
                        $value = $menu->getTopLeftBlockSku();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_left_sku_title:
                        $value = $menu->getTopLeftSkuTitle();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_right_block_sku:
                        $value = $menu->getTopRightBlockSku();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_right_sku_title:
                        $value = $menu->getTopRightSkuTitle();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_label:
                        $value = $menu->getLeftLabel();
                        if ($value == "new") {
                            $value = $this->getViewFileUrl('Cmsmart_Megamenu/images/icon_new.png');
                        }
                        if ($value == "hot") {
                            $value = $this->getViewFileUrl('Cmsmart_Megamenu/images/icon_hot.png');
                        }
                        if ($value == "sale") {
                            $value = $this->getViewFileUrl('Cmsmart_Megamenu/images/icon_sale.png');
                        }
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_cat_icon:
                        $value = $menu->getLeftCatIcon();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_block_top:
                        $value = $menu->getLeftBlockTop();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_block_left:
                        $value = $menu->getLeftBlockLeft();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_left_sku:
                        $value = $menu->getLeftLeftSku();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_block_right:
                        $value = $menu->getLeftBlockRight();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_right_sku:
                        $value = $menu->getLeftRightSku();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_sku:
                        $value = $menu->getLeftSku();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_content_type:
                        $value = $menu->getTopContentType();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_content_block:
                        $value = $menu->getTopContentBlock();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_pgrid_box_title:
                        $value = $menu->getTopPgridBoxTitle();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_pgrid_products:
                        $value = $menu->getTopPgridProducts();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_pgrid_num_columns:
                        $value = $menu->getTopPgridNumColumns();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::top_pgrid_cats:
                        $value = $menu->getTopPgridCats();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_content_block:
                        $value = $menu->getLeftContentBlock();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_content_type:
                        $value = $menu->getLeftContentType();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_pgrid_box_title:
                        $value = $menu->getLeftPgridBoxTitle();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_pgrid_products:
                        $value = $menu->getLeftPgridProducts();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_pgrid_num_columns:
                        $value = $menu->getLeftPgridNumColumns();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_pgrid_cats:
                        $value = $menu->getLeftPgridCats();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::position:
                        $value = $menu->getPosition();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_block_bottom:
                        $value = $menu->getLeftBlockBottom();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_label_container:
                        $value = $menu->getLeftLabelContainer();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_left_sku_title:
                        $value = $menu->getLeftLeftSkuTitle();
                        break;

                    case \Cmsmart\Megamenu\Model\Fields::left_right_sku_title:
                        $value = $menu->getLeftRightSkuTitle();
                        break;
                }
            }
        }
        return $value;
    }

    public function _getCatUrl($category)
    {
        return $this->_categoryHelper->getCategoryUrl($category);
    }

    public function getSubCategories($cat_id)
    {
        $categoryFactory = $this->_objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');

        $categories = $categoryFactory->create()
                    ->addAttributeToSelect('*')
                    ->addFieldToFilter("parent_id", $cat_id)
                    ->addFieldToFilter("is_active", 1)
                    ->addFieldToFilter("include_in_menu", 1)
                    ->setOrder('position', 'ASC');

        return $categories;
    }

    public function getHtmlContent($htmlContent)
    {
        return $this->_objectManagerr->get('Magento\Framework\Filter\Template')->getTemplateProcessor()->filter($htmlContent);
    }

    public function _getProductData($sku)
    {
        $product = $this->_productFactory->create();
        return $product->load($product->getIdBySku($sku));
    }

    public function _loadProduct($id)
    {
        $product = $this->_productFactory->create();
        return $product->load($id);
    }

    public function _loadCat($id)
    {
        $categoryFactory = $this->_objectManagerr->get('Magento\Catalog\Model\CategoryFactory');
        $category = $categoryFactory->create();
        return $category->load($id);
    }

    public function getMy()
    {
        return "test here";
    }

    public function showPrice($price)
    {
        return $this->_objectManagerr->get('\Magento\Framework\Pricing\Helper\Data')->currency($price, true, false);
    }

    public function _getAddToCartUrl($product)
    {
        $postDataHelper = $this->_objectManagerr->get('Magento\Framework\Data\Helper\PostHelper');
        $postData = $postDataHelper->getPostData($this->_objectManagerr->get('Magento\Checkout\Helper\Cart')->getAddUrl($product), ['product' => $product->getEntityId()]);
        return $postData;
    }
}
