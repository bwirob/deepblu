<?php
/*
* author: Netbase
*/
namespace Netbaseteam\Shopbybrand\Block;


class Request extends \Netbaseteam\Shopbybrand\Block\Brandhome
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