<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Marketplace\Ui\DataProvider\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedProductType;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class GroupedProductDataProvider extends ProductDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param StoreRepositoryInterface $storeRepository
     * @param ConfigInterface $config
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        ConfigInterface $config,
        StoreRepositoryInterface $storeRepository,
        \Netbaseteam\Marketplace\Block\Catalog\Product $sellerProduct,
        array $meta = [],
        array $data = [],
        array $addFieldStrategies = [],
        array $addFilterStrategies = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );

        $this->request = $request;
        $this->storeRepository = $storeRepository;
        $this->config = $config;
        $this->_sellerProduct = $sellerProduct;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $groupedProductIds = array();
        if($this->request->getParam('groupedproduct_ids')) {
            $groupedProductIds = $this->request->getParam('groupedproduct_ids');
        }
        $productSellerIds = $this->_sellerProduct->getProductCollection()->getAllIds();

        if (!$this->getCollection()->isLoaded()) {
            if (count($groupedProductIds)) {
                $this->getCollection()->addAttributeToFilter(
                    'type_id',
                    $this->config->getComposableTypes()
                )->addFieldToFilter('entity_id', array('nin' => $groupedProductIds))->addFieldToFilter('entity_id', array('in' => $productSellerIds));
            } else {
                $this->getCollection()->addAttributeToFilter(
                    'type_id',
                    $this->config->getComposableTypes()
                )->addFieldToFilter('entity_id', array('in' => $productSellerIds));
            }
            
            if ($storeId = $this->request->getParam('current_store_id')) {
                /** @var StoreInterface $store */
                $store = $this->storeRepository->getById($storeId);
                $this->getCollection()->setStore($store);
            }

            $this->getCollection()->load();
        }

        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }
}
