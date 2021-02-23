<?php

namespace Cmsmart\Categoryicon\Block;

/**
 * Categoryicon content block
 */
class Categoryicon extends \Magento\Framework\View\Element\Template
{
	protected $_objectManager;
	protected $_categoryHelper;
    /**
     * Categoryicon collection
     *
     * @var Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\Collection
     */
    protected $_categoryiconCollection = null;
    
    /**
     * Categoryicon factory
     *
     * @var \Cmsmart\Categoryicon\Model\CategoryiconFactory
     */
    protected $_categoryiconCollectionFactory;
    
    /** @var \Cmsmart\Categoryicon\Helper\Data */
    protected $_dataHelper;
    protected $_objectManagerr;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\CollectionFactory $categoryiconCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\CollectionFactory $categoryiconCollectionFactory,
        \Cmsmart\Categoryicon\Helper\Data $dataHelper,
		\Magento\Catalog\Helper\Category $categoryHelper,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_categoryiconCollectionFactory = $categoryiconCollectionFactory;
        $this->_dataHelper = $dataHelper;
		$this->_objectManager = $objectManager;
		$this->_categoryHelper = $categoryHelper;
		$this->_objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve categoryicon collection
     *
     * @return Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_categoryiconCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared categoryicon collection
     *
     * @return Cmsmart\Categoryicon\Model\ResourceModel\Categoryicon\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_categoryiconCollection)) {
            $this->_categoryiconCollection = $this->_getCollection();
            $this->_categoryiconCollection->setCurPage($this->getCurrentPage());
            $this->_categoryiconCollection->setPageSize($this->_dataHelper->getCategoryiconPerPage());
            $this->_categoryiconCollection->setOrder('published_at','asc');
        }

        return $this->_categoryiconCollection;
    }
    
    /**
     * Fetch the current page for the categoryicon list
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
     * @param Cmsmart\Categoryicon\Model\Categoryicon $categoryiconItem
     * @return string
     */
    public function getItemUrl($categoryiconItem)
    {
        return $this->getUrl('*/*/view', array('id' => $categoryiconItem->getId()));
    }
    
    /**
     * Return URL for resized Categoryicon Item image
     *
     * @param Cmsmart\Categoryicon\Model\Categoryicon $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('categoryicon_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $categoryiconPerPage = $this->_dataHelper->getCategoryiconPerPage();

            $pager->setAvailableLimit([$categoryiconPerPage => $categoryiconPerPage]);
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(TRUE);
            $pager->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            return $pager->toHtml();
        }

        return NULL;
    }
	
	public function getSubCategories(){
		$categoryFactory = $this->_objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
		$helper = $this->_objectManagerr->get('\Cmsmart\Categoryicon\Helper\Data');
		
		$categories = $categoryFactory->create()                              
					->addAttributeToSelect('*')
					->addFieldToFilter("parent_id", $helper->getRootCatID())
					->addFieldToFilter("is_active", 1)
					->addFieldToFilter("include_in_menu", 1)
					->setOrder('position', 'ASC')
					;
		
		return $categories;
	}
	
	public function _getCatUrl($category){
		return $this->_categoryHelper->getCategoryUrl($category);
	}
	
	public function _loadCat($id){
		$categoryFactory = $this->_objectManager->get('Magento\Catalog\Model\CategoryFactory');
		$category = $categoryFactory->create();  
		return $category->load($id);
	}
	
	public function getCurrentCategoryId(){
		$categoryRegistry = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_category');
		$currentCateId = 0;
		if($categoryRegistry) {
			$currentCateId = $categoryRegistry->getId();
		}
		
		return $currentCateId;
	}
	
	public function getCategories($myCatId = null){
		/* get current category id */
		$arrayCategories = array();
		$currentCateId = $this->getCurrentCategoryId();
		if($myCatId != null )$currentCateId = $myCatId;
		if($currentCateId) {
			$category = $this->_objectManager->create('Magento\Catalog\Model\Category');
			$tree = $category->getTreeModel();
			$tree->load();
			$ids = $tree->getCollection()->getAllIds();
			
			if ($ids) {
				$i = 0;
				foreach ($ids as $id) {
					$cat = $this->_objectManager->create('Magento\Catalog\Model\Category');
					$cat->load($id);
		 
					if($cat->getParentId() == $currentCateId){
						$arrayCategories[$i] =
								array("parent_id" => $cat->getParentId(),
									"name" => $cat->getName(),
									"cat_id" => $cat->getId(),
									"cat_level" => $cat->getLevel(),
									"cat_url" => $cat->getUrl()
						);
						$i++;
					}
				} // for each ends	
			}//if ids present
		}
		
		return $arrayCategories;
	}
}
