<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Marketplace\Ui\DataProvider\Product;


/**
 * Class ProductDataProvider
 *
 * @api
 * @since 100.0.2
 */
class ProductDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var PoolInterface
     */
    private $modifiersPool;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $modifiersPool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Netbaseteam\Marketplace\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Netbaseteam\Marketplace\Block\Catalog\Product $mpProduct,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->mpProduct = $mpProduct;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->addFieldToFilter('id', array('in' => $this->mpProduct->getMpCollection()->getAllIds()));
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        $data = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => $items['items'],
        ];
        
        return $data;
    }
}
