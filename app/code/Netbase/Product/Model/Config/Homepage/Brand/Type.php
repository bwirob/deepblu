<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Product\Model\Config\Homepage\Brand;

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

        $brandImg01 = '<img src="'.$mediaUrl.'sun/liveview/brand-01.jpg" alt="brand" />';

        return [
            ['value' => 'brand_view.phtml', 'label' => __($brandImg01)]
        ];
    }
}
