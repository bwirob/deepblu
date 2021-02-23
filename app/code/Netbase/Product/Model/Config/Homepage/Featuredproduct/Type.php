<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Product\Model\Config\Homepage\Featuredproduct;

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

        $featuredImg01 = '<img src="'.$mediaUrl.'sun/liveview/featured-01.jpg" alt="Featured" />';

        return [
            ['value' => 'Featured.phtml', 'label' => __($featuredImg01)]
        ];
    }
}
