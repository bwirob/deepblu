<?php

namespace Netbaseteam\Locator\Block\Store;

class Sidebar extends \Netbaseteam\Locator\Block\Store
{
    
    public function toStoreInfoJson(){
        $info = $this->getStoreContactInfo();
        $data = $this->_jsonHelper->jsonEncode($info);
        return $data;
        
    }

    public function toWorkTimeHtml($open,$close){
    	$hmsOpen = explode(',',$open);
    	$hmsClose = explode(',',$close);
    	$html = $hmsOpen[0].':'.$hmsOpen[1].' to '.$hmsClose[0].':'.$hmsClose[1];
    	return $html;
    }

     public function getStoreContactInfo(){
        $store = $this->getStore();
        $info = array();
        $info['store_name'] = $store->getStoreName();
        $info['store_link'] = $store->getStoreLink();
        $info['phone_number'] = $store->getPhoneNumber();
        $info['fax_number'] = $store->getFaxNumber();
        $info['address'] = $store->getAddress();
        $info['email'] = $store->getEmail();
        return $info;  
    }


}