<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Product\Model\Config\Homepage\Slider;

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

        $sliderImg01 = '<img src="'.$mediaUrl.'sun/liveview/slider-01.jpg" alt="slider" />';
        $sliderImg02 = '<img src="'.$mediaUrl.'sun/liveview/slider-02.jpg" alt="slider" />';
        $sliderImg03 = '<img src="'.$mediaUrl.'sun/liveview/slider-03.jpg" alt="slider" />';
        $sliderImg04 = '<img src="'.$mediaUrl.'sun/liveview/slider-04.jpg" alt="slider" />';
        $sliderImg05 = '<img src="'.$mediaUrl.'sun/liveview/slider-05.jpg" alt="slider" />';

        return [
            ['value' => 'home_slider_01', 'label' => __($sliderImg01)],
            ['value' => 'home_slider_02', 'label' => __($sliderImg02)],
            ['value' => 'home_slider_03', 'label' => __($sliderImg03)],
            ['value' => 'home_slider_04', 'label' => __($sliderImg04)],
            ['value' => 'home_slider_05', 'label' => __($sliderImg05)]
        ];
    }
}
