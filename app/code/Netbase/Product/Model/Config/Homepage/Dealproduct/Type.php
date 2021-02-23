<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Product\Model\Config\Homepage\Dealproduct;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('\Netbase\Product\Helper\Data');
        $mediaUrl = $helper->getBaseMediaUrl();

        $dealImg01 = '<img src="'.$mediaUrl.'sun/liveview/deal-01.jpg" alt="deal" />';

        return [
            ['value' => 'Deals.phtml', 'label' => __($dealImg01)]
        ];
    }
}
