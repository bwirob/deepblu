<?php

namespace Netbaseteam\Locator\Block\Locator;

class Storelist extends \Netbaseteam\Locator\Block\Locator
{



	public function getCollection()
    {
        $collection = $this->_getCollection();
        $collection->addFieldToFilter('status', array('eq'=>'1'))
                    ->setOrder('ordering','ASC');
        return $collection; 
    }

    public function getStoresData(){
        $localtor = $this->getCollection();
        return $localtor->getData();
    }

    public function hasStore(){
    	$store = $this->getStoresData();
    	if(count($store)>0){
    		return true;
    	}
    	return false;
    }

    public function countStore(){
    	$store = $this->getStoresData();
    	$numStore = count($store);
    	return $numStore;
    }

    public function getStoreImgDir($flieName){
    	return $this->_dataHelper->getBaseUrl().'/'.$flieName;
    }

	public function getStoreUrl($localtorItem){
    	
        return $this->_dataHelper->getBaseFontUrl().'locator/'.$localtorItem['identifier'];
	}


    
    

    public function toPositionJson($store){
        $position = array();
        $position['lat'] = floatval($store['latitude']);
        $position['lng'] = floatval($store['longitude']);
        $position = json_encode($position);
        return $position;

    }

    public function toStoreJsonData($store){
        $storeInfo = array();
        $storeInfo['store_name'] = $store['store_name'];
        $storeInfo['store_link'] = $store['store_link'];
        $storeInfo['phone_number'] = $store['phone_number'];
        $storeInfo['fax_number'] = $store['fax_number'];
        $storeInfo['address'] = $store['address'];
        
        $storeInfo['store_id'] =  intval($store['localtor_id']);
        $storeInfo = json_encode($storeInfo);
        return $storeInfo;

    }

    public function toZoonLevelJsonData($store){
        $level = array();
        $level['zoom_level'] = intval($store['zoom_level']);
        $level = json_encode($level);
        return $level;

    }
    



}