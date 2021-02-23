<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Product\Model\Config\Homepage\Banner;

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

        $bannerImg01 = '<img src="'.$mediaUrl.'sun/liveview/banner-01.jpg" alt="banner" />';
        $bannerImg02 = '<img src="'.$mediaUrl.'sun/liveview/banner-02.jpg" alt="banner" />';
        $bannerImg03 = '<img src="'.$mediaUrl.'sun/liveview/banner-03.jpg" alt="banner" />';

        return [
            ['value' => 'home_banner_01', 'label' => __($bannerImg01)],
            ['value' => 'home_banner_02', 'label' => __($bannerImg02)],
            ['value' => 'home_banner_03', 'label' => __($bannerImg03)]
        ];
    }
}