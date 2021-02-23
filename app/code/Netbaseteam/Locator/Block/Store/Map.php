<?php

namespace Netbaseteam\Locator\Block\Store;

class Map extends \Netbaseteam\Locator\Block\Store
{
    
	public function getImageUrl($item, $width){
	    return $this->_dataHelper->resize($item, $width);
	}
	public function getCountrysData(){
		$countryData = $this->_countryFactory->create()->toOptionArray();
		unset($countryData[0]);
		return $countryData;
	}


}