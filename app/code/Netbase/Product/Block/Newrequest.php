<?php
/*
* author: Netbase
*/
namespace Netbase\Product\Block;

class Newrequest extends \Netbase\Product\Block\NewProduct
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
