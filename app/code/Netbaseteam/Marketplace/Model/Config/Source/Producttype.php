<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Model\Config\Source;

/**
 * Used in creating options for getting product type value.
 */
class Producttype
{
    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $_config;

    /**
     * Construct.
     *
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     */
    public function __construct(
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
    )
    {
        $this->productTypeConfig = $config;
    }

    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $productTypes = [];
        foreach ($this->productTypeConfig->getAll() as $productTypeData) {
            $productTypes[] =
                [
                    'value' => $productTypeData['name'],
                    'label' => $productTypeData['label']
                ];
        }
        return $productTypes;
    }
}
