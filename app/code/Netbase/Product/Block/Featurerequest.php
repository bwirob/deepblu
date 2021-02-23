<?php
/*
* author: Netbase
*/
namespace Netbase\Product\Block;


class Featurerequest extends \Netbase\Product\Block\ProductByAttribute
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