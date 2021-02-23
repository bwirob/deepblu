<?php

namespace Netbaseteam\Locator\Block;

class Locator extends Abstractlocaltor
{
    
    
    protected function _getCollection()
    {
        $collection = $this->_localtorFactory->create()->getCollection();
        return $collection;
    }
  	
  	public function getActionForm(){
	    return $this->getUrl('*/search/index' );
	}

	public function getHiperlinkUrl($url){
		if(strpos( $url,'http://')!==false||strpos( $url, 'https://')!==false){
			return $url;
		}

		return 'http://'.$url;
	}
    
    
    
    
}
