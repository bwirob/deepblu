<?php

namespace Netbaseteam\Locator\Block;

class Store extends Abstractlocaltor
{
    
    public function getStore(){
       return $this->_coreRegistry->registry('store');
    }

    public function getLatitude(){
    	$store = $this->getStore();
    	$lat = (float)$store->getLatitude();
    	return $lat;
    }

    public function getLongitude(){
    	$store = $this->getStore();
    	$lng = (float)$store->getLongitude();
    	return $lng;
    }

   	public function getZoomLevel(){
    	$store = $this->getStore();
    	$zoomLv = (int)$store->getZoomLevel();
    	return $zoomLv;
    }

    public function getStoreInfo(){
        $store = $this->getStore();
        $info = array();
        $info['store_name'] = $store->getStoreName();
        $info['description'] = $store->getDescription();
        $info['store_link'] = $store->getStoreLink();
        $info['phone_number'] = $store->getPhoneNumber();
        $info['fax_number'] = $store->getFaxNumber();
        $info['address'] = $store->getAddress();
        $info['email'] = $store->getEmail();
        return $info;  
    }

    public function getScheduleId(){
        $store = $this->getStore();
        $scheduleId = $store->getScheduleId();
        if(!empty($scheduleId)){
            return $scheduleId;
        }
        return '';
                    
    }

    public function _getScheduleData(){
        $scheduleId = (int)$this->getScheduleId();
        if($scheduleId){
            $WDcollection = $this->_workdateFactory->create()->getCollection();
            $WDcollection->addFieldToFilter('schedule_id', array('eq'=>$scheduleId));
            return $WDcollection;
        }
        return false;
        
    }

    public function getScheduleData(){
        $cheduleData = $this->_getScheduleData();
        $data = array();

        if($cheduleData){
            $i = 0;
            foreach ($cheduleData as $key => $chedule) {
                $openkey = 'd'.$i.'_open';
                $closekey = 'd'.$i.'_close';
                $statuskey = 'd'.$i.'_status';
                $data[$openkey] = $chedule->getOpenTime();
                $data[$closekey] = $chedule->getCloseTime();
                $data[$statuskey] = $chedule->getStatus();
                $i++;
            }

            return $data;
        }
        return false;
    }

    

    
    

    
    
    
}
