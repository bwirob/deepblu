<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Product\Model\Config\Featured;

class Categories implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
        $categoryFactory = $objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $categories = $categoryFactory->create()
                                ->addAttributeToSelect('*');
        
        foreach ($categories as $category):
            $options[] = array(
                'value' => $category->getId(),
                'label' => $category->getName(),
            );
        endforeach;

        return $options;
    }
}
