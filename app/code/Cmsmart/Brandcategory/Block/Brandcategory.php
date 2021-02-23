<?php

namespace Cmsmart\Brandcategory\Block;

/**
 * Brandcategory content block
 */
class Brandcategory extends \Magento\Framework\View\Element\Template
{
    /**
     * Brandcategory collection
     *
     * @var Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory\Collection
     */
    protected $_brandcategoryCollection = null;
    
    /**
     * Brandcategory factory
     *
     * @var \Cmsmart\Brandcategory\Model\BrandcategoryFactory
     */
    protected $_brandcategoryCollectionFactory;
    
    /** @var \Cmsmart\Brandcategory\Helper\Data */
    protected $_dataHelper;
	
	protected $_categoryFactory;
	protected $_productCollectionFactory;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory\CollectionFactory $brandcategoryCollectionFactory
     * @param array $data
     */
    public function __construct(
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory\CollectionFactory $brandcategoryCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Cmsmart\Brandcategory\Helper\Data $dataHelper,
        array $data = []
    ) {
		$this->_productCollectionFactory = $productCollectionFactory;
        $this->_brandcategoryCollectionFactory = $brandcategoryCollectionFactory;
        $this->_dataHelper = $dataHelper;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve brandcategory collection
     *
     * @return Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_brandcategoryCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared brandcategory collection
     *
     * @return Cmsmart\Brandcategory\Model\ResourceModel\Brandcategory\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_brandcategoryCollection)) {
            $this->_brandcategoryCollection = 
						$this->_getCollection()
								->setOrder('position','ASC')
								->addFieldToFilter('status', \Cmsmart\Brandcategory\Model\Brandcategory::STATUS_ENABLED);
            /* $this->_brandcategoryCollection->setCurPage($this->getCurrentPage());
            $this->_brandcategoryCollection->setPageSize($this->_dataHelper->getBrandcategoryPerPage());
            $this->_brandcategoryCollection->setOrder('published_at','asc'); */
        }

        return $this->_brandcategoryCollection;
    }
    
    /**
     * Fetch the current page for the brandcategory list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param Cmsmart\Brandcategory\Model\Brandcategory $brandcategoryItem
     * @return string
     */
    public function getItemUrl($brandcategoryItem)
    {
        return $this->getUrl('*/*/view', array('id' => $brandcategoryItem->getId()));
    }
    
    /**
     * Return URL for resized Brandcategory Item image
     *
     * @param Cmsmart\Brandcategory\Model\Brandcategory $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }

	public function _loadCat($id){
		$category = $this->_categoryFactory->create();  
		return $category->load($id);
	}
	
	public function getProductCollection($cat_id)
	{
		$collection = $this->_productCollectionFactory->create();
		$collection->addAttributeToSelect('*');
		$collection->addCategoriesFilter(['eq' => $cat_id]);
		$collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
		$collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
		
		return $collection;
	}

	public function _getBrands($cat_id){
		
		if($cat_id > 0) {
			$pCatCollection = $this->getProductCollection($cat_id);
			$p_cats = array();
			foreach($pCatCollection as $p){
				$p_cats[] = $p->getId();
			}
		}
		$pBrandCollection = $this->getCollection();
		$brands_arr = null;
		foreach($pBrandCollection as $b){
			if($cat_id > 0) {
				$pid_arr = explode(",", $b->getProducts());
				$count_pid_arr = count($pid_arr);
				for($i=0; $i < $count_pid_arr; $i++){
					if(in_array($pid_arr[$i], $p_cats)){
						$brands_arr[] = array(
								"", /* url */
								$b->getLogo(),
								$b->getBrandName()
							);
						break;
					}
				}
			} else {
				$brands_arr[] = array(
						"", /* url */
						$b->getLogo(),
						$b->getBrandName()
					);
			}
		}
		return $brands_arr;
	}
}
