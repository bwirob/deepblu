<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Product\Model\Config\Homepage\Policy;

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

        $policyImg01 = '<img src="'.$mediaUrl.'sun/liveview/policy-01.jpg" alt="policy" />';
        $policyImg02 = '<img src="'.$mediaUrl.'sun/liveview/policy-02.jpg" alt="policy" />';

        return [
            ['value' => 'home_policy_01', 'label' => __($policyImg01)],
            ['value' => 'home_policy_02', 'label' => __($policyImg02)]
        ];
    }
}
