<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Ui\Component\Listing\Columns\Product;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedType;

class Type extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'column.type_id';

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
        $this->productFactory = $productFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $productID = '';
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $productID = $item['product_id'];
                $product = $this->productFactory->create()->load($productID);
                $item[$fieldName] = $this->getProductType($product->getTypeId());
            }
        }

        return $dataSource;
    }

    public function getProductType($typeId) {
        switch ($typeId) {
            case ProductType::TYPE_SIMPLE:
                $type = __('Simple Product');
                break;
            case ProductType::TYPE_BUNDLE:
                $type = __('Bundle Product');
                break;
            case ProductType::TYPE_VIRTUAL:
                $type = __('Virtual Product');
                break;
            case DownloadableType::TYPE_DOWNLOADABLE:
                $type = __('Virtual Product');
                break;
            case ConfigurableType::TYPE_CODE:
                $type = __('Configurable Product');
                break;
            case GroupedType::TYPE_CODE:
                $type = __('Grouped Product');
                break;
            default:
                $type = __('Grouped Product');
        }
        return $type;
    }
}
