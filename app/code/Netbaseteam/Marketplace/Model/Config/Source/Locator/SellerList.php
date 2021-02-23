<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Model\Config\Source\Locator;

class SellerList
{

    public function __construct(
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory
    )
    {
        $this->_sellerdataFactory = $sellerdataFactory;
    }

    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_sellerdataFactory->create()->getCollection();
        $data[] = ['label' => '', 'value' => ''];

        foreach ($collection as $item) {
            $data[] = [
                'label' => $item->getShopTitle(),
                'value' => $item->getSellerId(),
            ];
        }

        return $data;
    }
}
