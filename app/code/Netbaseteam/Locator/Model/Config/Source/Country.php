<?php

namespace Netbaseteam\Locator\Model\Config\Source;

class Country implements \Magento\Framework\Option\ArrayInterface
{
    const local     = 'LOCAL';
    const url   = 'URL';
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $countryModel = $objectManager->get('\Magento\Directory\Model\ResourceModel\Country\CollectionFactory')->create();
        $options = $countryModel->toOptionArray();
        unset($options[0]);
        return $options;
    }
}

