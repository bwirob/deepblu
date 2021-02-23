<?php
/*
* author: Netbase
*/
namespace Netbase\Product\Block;


class Bestsellerrequest extends \Netbase\Product\Block\Bestseller
{
    
    public function getCategoryId()
    {

        $data = $this->_coreRegistry->registry('dataRequest');
        
        return $data['categoryId'];
    }
    public function getHomeName()
    {
        $data = $this->_coreRegistry->registry('dataRequest');

        return $data['home'];
    }
}
?>