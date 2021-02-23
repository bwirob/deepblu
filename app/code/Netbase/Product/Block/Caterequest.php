<?php
/*
* author: Netbase
*/
namespace Netbase\Product\Block;


class Caterequest extends \Netbase\Product\Block\Deals
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