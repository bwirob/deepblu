<?php

namespace Netbaseteam\Locator\Block\Store;

class Storeinfo extends \Netbaseteam\Locator\Block\Store
{
    
	public function getStoreName(){
       	$store = $this->getStore();
       	$storeName = $store->getStoreName();
       	return $storeName;
    }

    public function getStoreImage(){
	    $store = $this->getStore();
	    if(!$store->getStoreImage()){
	    	return false;
	    }
	    return $this->_dataHelper->getBaseUrl().'/'.$store->getStoreImage();
    }

   

    

}