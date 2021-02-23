<?php


namespace Netbaseteam\Ajaxsearch\Block\Catalog\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
	protected function _getProductCollection()
    {
		if(!empty($this->getRequest()->getParam('nbsearch'))){
			$cat_ids_arr = explode(",", $this->getRequest()->getParam('cat'));
			$collection = parent::_getProductCollection()
					->addAttributeToSelect('*')
					->addCategoriesFilter(['in' => $cat_ids_arr]);
			return $collection;
		} else {
			return parent::_getProductCollection();
		}
	}
    
}
