<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cmsmart\Brandcategory\Model\Config\Featured;

class Categories implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
    {
		$arrayCategories = $this->top_get_categories();
		$options = "";
		$this->top_createTree($arrayCategories, 0, $options);
		$options_arr = explode(";", trim($options, ";"));
		$options_ret = array();
		for($i=0; $i<count($options_arr);$i++){
			$el = explode("|", $options_arr[$i]);
			$options_ret[] = array(
                'value' => $el[0],
                'label' => $el[1]
            );
		}
		
		return $options_ret;
	}
		
	public function top_get_categories() {
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		$category = $objectManagerr->create('Magento\Catalog\Model\Category');
		$tree = $category->getTreeModel();
		$tree->load();
		$ids = $tree->getCollection()->getAllIds();
		$arrayCategories = array();
		if ($ids) {
			$count = 0;
			foreach ($ids as $id) {
				$cat = $objectManagerr->create('Magento\Catalog\Model\Category');
				$cat->load($id);
	 
			    //if($id!=1){
				  
				$level = $cat->getLevel();
				$label = "";
				if($level > 0){
					if($level == 1)$count = 2;
					elseif($level == 2)$count = 4;
					elseif($level == 3)$count = 8;
					elseif($level == 4)$count = 16;
					elseif($level == 5)$count = 32;
					elseif($level == 6)$count = 64;
					$label = str_repeat("-", $count).$cat->getName();
				} else {
					$label = $cat->getName();
				}
	 
				$arrayCategories[$id] =
						array(
							"parent_id" => $cat->getParentId(),
							"name" => $label,
							"cat_id" => $cat->getId(),
							"cat_level" => $cat->getLevel(),
							"cat_url" => $cat->getUrl()
				);
			  //  }
			}// for each ends
			return $arrayCategories;
		}//if ids present
	}
	
	public function top_createTree($array, $currentParent, &$var, $currLevel = 0, $prevLevel = -1) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		foreach ($array as $categoryId => $category) {
			if ($currentParent == $category['parent_id']) {
				/* $options[] = array(
					'value' => $categoryId,
					'label' => $category['name'],
				);	 */	
				$var .= $categoryId."|".$category['name'].";";
				
				if ($currLevel > $prevLevel) {
					$prevLevel = $currLevel;
				}
				$currLevel++;
				$this->top_createTree($array, $categoryId, $var, $currLevel, $prevLevel);
				$currLevel--;
			}
		}
		
		return $var;
	}
}
