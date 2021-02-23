<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\Config\Source;

class Sellers
{

    public function __construct(
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory
    ){
        $this->_sellerdataFactory = $sellerdataFactory;
    }

    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $sellers = [];
        foreach ($this->getSellers() as $sellerData) {
            $sellers[] =
                [
                    'value' => $sellerData['seller_id'],
                    'label' => $sellerData['shop_title']
                ];
        }
        return $sellers;
    }

    public function getSellers() {
        return $this->_sellerdataFactory->create()->getCollection();
    }
}
