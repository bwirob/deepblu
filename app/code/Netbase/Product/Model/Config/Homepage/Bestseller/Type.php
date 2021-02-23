<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Product\Model\Config\Homepage\Bestseller;

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

        $bestsellerImg01 = '<img src="'.$mediaUrl.'sun/liveview/bestseller-01.jpg" alt="banner" />';
        /*$bestsellerImg02 = '<img src="'.$mediaUrl.'sun/liveview/bestseller-02.jpg" alt="banner" />';
        $bestsellerImg03 = '<img src="'.$mediaUrl.'sun/liveview/bestseller-03.jpg" alt="banner" />';*/

        return [
            ['value' => 'Bestseller.phtml', 'label' => __($bestsellerImg01)]/*,
            ['value' => 'home_banner_02', 'label' => __($bannerImg02)],
            ['value' => 'home_banner_03', 'label' => __($bannerImg03)]*/
        ];
    }
}
