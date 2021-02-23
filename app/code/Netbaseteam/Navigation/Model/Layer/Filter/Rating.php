<?php
namespace Netbaseteam\Navigation\Model\Layer\Filter;


use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\CategoryFactory as CategoryModelFactory;
use Magento\Catalog\Model\Layer;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Layer\Filter\DataProvider\Category as CategoryDataProvider;
/**
 * Layer category filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rating extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{
    const RATING_COLLECTION_FLAG = 'cmsmart_rating_filter_applied';
    const CONFIG_FILTER_LABEL_PATH = 'cmsmart_navigation/rating_filter/label';

    /**
     * Active Category Id
     *
     * @var int
     */
    protected $_categoryId;

    protected $_activeFilter = false;

    /**
     * Applied Category
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $_appliedCategory;

    /**
     * Core data
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var CategoryDataProvider
     */
    private $dataProvider;

    protected $_scopeConfig;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param CategoryFactory $categoryDataProviderFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->_escaper = $escaper;
        $this->_scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
        $this->_productModel = $productModel;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->_requestVar = 'rat';
        $this->dataProvider = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
    }


    /**
     * Get filter value for reset current filter state
     *
     * @return mixed|null
     */
    public function getResetValue()
    {
        return $this->dataProvider->getResetValue();
    }


    /**
     * Apply category filter to layer
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filter = $request->getParam($this->getRequestVar(), null);
        if (is_null($filter)) {
            return $this;
        }

        $this->_activeFilter = true;

        $filter = explode('-', $filter);
        list($from, $to) = $filter;
        $collection = $this->getLayer()->getProductCollection();
        $collection->setFlag(self::RATING_COLLECTION_FLAG, true);
        $collection->getSelect()->joinLeft(array('rova'=> 'rating_option_vote_aggregated'),'e.entity_id =rova.entity_pk_value',array("percent"))
            ->where("rova.percent between ".$from." and ".$to)
            ->group('e.entity_id');

//        $collection->printlogquery(true);die;
        return $this;
    }

    /**
     * Get filter name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getName()
    {
        return $this->_scopeConfig->getValue(
            self::CONFIG_FILTER_LABEL_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**
     * Get data array for building attribute filter items
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    protected function _getItemsData()
    {
        $s1='<div class="rating-summary" style="display: inline-block;margin-top: -5px;">
                                        <div class="rating-result" title="20%">
                                            <span style="width:20%"><span>1</span></span>
                                        </div>
                                    </div>';

        $s2='<div class="rating-summary" style="display: inline-block;margin-top: -5px;">
                                        <div class="rating-result" title="40%">
                                            <span style="width:40%"><span>2</span></span>
                                        </div>
                                    </div>';

        $s3='<div class="rating-summary" style="display: inline-block;margin-top: -5px;">
                                        <div class="rating-result" title="60%">
                                            <span style="width:60%"><span>3</span></span>
                                        </div>
                                    </div>';

        $s4='<div class="rating-summary" style="display: inline-block;margin-top: -5px;">
                                        <div class="rating-result" title="80%">
                                            <span style="width:80%"><span>4</span></span>
                                        </div>
                                    </div>';

        $s5='<div class="rating-summary" style="display: inline-block;margin-top: -5px;">
                                        <div class="rating-result" title="100%">
                                            <span style="width:100%"><span>5</span></span>
                                        </div>
                                    </div>';


        $facets = array(
            '0-20'=>$s1,
            '21-40'=>$s2,
            '41-60'=>$s3,
            '61-80'=>$s4,
            '81-100'=>$s5,
        );

        $data = [];
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if (count($facets) > 1) { // two range minimum
            $i=1;


            foreach ($facets as $key => $label) {

                $count='';
                $filter = explode('-', $key);
                list($from, $to) = $filter;

                $collection = $this->getProductCollection();

                $collection->getSelect()->joinLeft(array('rova'.$i=> 'rating_option_vote_aggregated'),'e.entity_id =rova'.$i.'.entity_pk_value',array("percent"))
                    ->where("rova".$i.".percent between ".$from." and ".$to)
                    ->group('e.entity_id');

                $count=count($collection);

                $i++;

                if($count > 0){
                    $this->itemDataBuilder->addItemData(
                    //$this->_escaper->escapeHtml($label),
                        $label,
                        $key,
                        $count
                    );

                    $count=0;
                }
            }
        }

        return $this->itemDataBuilder->build();
    }

    public function getCategory()
    {
        $category = $this->objectManager->get('Magento\Framework\Registry')->registry('current_category');
        $categoryId = $category->getId();
        $category = $this->categoryFactory->create()->load($categoryId);
        return $category;
    }
    public function getProductCollection()
    {
        return $this->getCategory()->getProductCollection()->addAttributeToSelect('*');
    }

}
