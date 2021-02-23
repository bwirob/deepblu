<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Sun\Helper;

use Magento\Framework\Registry;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_objectManager;
    private $_registry;
    protected $_filterProvider;
	protected $_filesystem ; 
    protected $_imageFactory; 
    public function __construct(
		\Magento\Framework\Filesystem $filesystem,         
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        Registry $registry
    ){
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_filterProvider = $filterProvider;
        $this->_registry = $registry;
		$this->_filesystem = $filesystem;
		$this->_imageFactory = $imageFactory;
        
        parent::__construct($context);
    }
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    public function getConfig($config_path)
    {
		
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getModel($model) {
        return $this->_objectManager->create($model);
    }
    public function getCurrentStore() {
        return $this->_storeManager->getStore();
    }
    public function filterContent($content) {
        return $this->_filterProvider->getPageFilter()->filter($content);
    }
    public function getCategoryProductIds($current_category) {
        $category_products = $current_category->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_saleable', 1, 'left')
            ->addAttributeToSort('position','asc');
        $cat_prod_ids = $category_products->getAllIds();
        
        return $cat_prod_ids;
    }
    public function getPrevProduct($product) {
        $current_category = $product->getCategory();
        if(!$current_category) {
            foreach($product->getCategoryCollection() as $parent_cat) {
                $current_category = $parent_cat;
            }
        }
        if(!$current_category)
            return false;
        $cat_prod_ids = $this->getCategoryProductIds($current_category);
        $_pos = array_search($product->getId(), $cat_prod_ids);
        if (isset($cat_prod_ids[$_pos - 1])) {
            $prev_product = $this->getModel('Magento\Catalog\Model\Product')->load($cat_prod_ids[$_pos - 1]);
            return $prev_product;
        }
        return false;
    }
    public function getNextProduct($product) {
        $current_category = $product->getCategory();
        if(!$current_category) {
            foreach($product->getCategoryCollection() as $parent_cat) {
                $current_category = $parent_cat;
            }
        }
        if(!$current_category)
            return false;
        $cat_prod_ids = $this->getCategoryProductIds($current_category);
        $_pos = array_search($product->getId(), $cat_prod_ids);
        if (isset($cat_prod_ids[$_pos + 1])) {
            $next_product = $this->getModel('Magento\Catalog\Model\Product')->load($cat_prod_ids[$_pos + 1]);
            return $next_product;
        }
        return false;
    }
	public function getCategories(){
		/* get current category id */
		$categoryRegistry = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_category');
		$arrayCategories = array();
		if($categoryRegistry) {
			$currentCateId = $categoryRegistry->getId();
			
			$category = $this->_objectManager->create('Magento\Catalog\Model\Category');
			$tree = $category->getTreeModel();
			$tree->load();
			$ids = $tree->getCollection()->getAllIds();
			$arr = array();
			if ($ids) {
				foreach ($ids as $id) {
					$cat = $this->_objectManager->create('Magento\Catalog\Model\Category');
					$cat->load($id);
		 
					if($cat->getParentId() == $currentCateId){
						$arrayCategories[$id] =
								array("parent_id" => $cat->getParentId(),
									"name" => $cat->getName(),
									"cat_id" => $cat->getId(),
									"cat_level" => $cat->getLevel(),
									"cat_url" => $cat->getUrl()
						);
					}
				}// for each ends
				
			}//if ids present
		}
		return $arrayCategories;
	}
	public function getcurrentcategory(){
		$categoryRegistry = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_category');
		if($categoryRegistry) {
			$categoryname = $categoryRegistry->getName();
			return $categoryname;
		}
	}
	public function resizeImg($image, $width = null, $height = null)
    {
		$absolutePath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('sun/header/').$image;

        $imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/'.$width.'/').$image;         
        if(!empty($image)){
            $imageResize = $this->_imageFactory->create();         
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(TRUE);         
            $imageResize->keepTransparency(TRUE);         
            $imageResize->keepFrame(FALSE);         
            $imageResize->keepAspectRatio(FALSE);         
            $imageResize->resize($width,$height);  
            //destination folder                
            $destination = $imageResized ;    
            //save image      
            $imageResize->save($destination);
        }         

        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'resized/'.$width.'/'.$image;
        return $resizedURL;
    }
}
