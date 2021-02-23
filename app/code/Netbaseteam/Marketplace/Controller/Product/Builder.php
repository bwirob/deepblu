<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
declare(strict_types=1);

namespace Netbaseteam\Marketplace\Controller\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Cms\Model\Wysiwyg as WysiwygModel;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreFactory;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as Product;
use Magento\Catalog\Model\Product\Type as ProductTypes;
/**
 * Netbaseteam Marketplace Product Builder Controller Class.
 */
class Builder
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Registry           $registry
     * @param \Psr\Log\LoggerInterface              $loggerInterface
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        \Psr\Log\LoggerInterface $loggerInterface,
        ProductRepositoryInterface $productRepository = null
    ) {
        $this->_productFactory = $productFactory;
        $this->_logger = $loggerInterface;
        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->productRepository = $productRepository ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ProductRepositoryInterface::class);
    }

    /**
     * Build product based on requestData.
     *
     * @param $requestData
     *
     * @return \Magento\Catalog\Model\Product $product
     */
    public function build($requestData, $storeId = 0)
    {
        $productId = isset($requestData['id']) ? (int) $requestData['id'] : '';
        $attributeSetId = isset($requestData['set']) ? (int) $requestData['set'] : '';
        $typeId = isset($requestData['type']) ? $requestData['type'] : '';
        if ($productId) {
            try {
                $isPartner = $this->_helper->getSellerId();
                $flag = false;
                if ($isPartner) {
                    $rightseller = $this->_helper->isCorrectSeller($productId);
                    if ($rightseller == 1) {
                        $flag = true;
                    }
                }
                if ($flag) {
                    $product = $this->productRepository->getById($productId, true, $storeId);
                    if ($attributeSetId) {
                        $product->setAttributeSetId($attributeSetId);
                    }
                    $product->load($productId);
                }
            } catch (\Exception $e) {
                $product = $this->createEmptyProduct(ProductTypes::DEFAULT_TYPE, $attributeSetId, $storeId);
                $this->logger->critical($e);
            }
        } else {
            $product = $this->createEmptyProduct($typeId, $attributeSetId, $storeId);
        }
        $this->_registry->register('product', $product);
        $this->_registry->register('current_product', $product);

        return $product;
    }

    /**
     * Create a product with the given properties
     *
     * @param int $typeId
     * @param int $attributeSetId
     * @param int $storeId
     * @return \Magento\Catalog\Model\Product
     */
    private function createEmptyProduct($typeId, $attributeSetId, $storeId): Product
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_productFactory->create();
        $product->setData('_edit_mode', true);

        if ($typeId !== null) {
            $product->setTypeId($typeId);
        }

        if ($storeId !== null) {
            $product->setStoreId($storeId);
        }

        if ($attributeSetId) {
            $product->setAttributeSetId($attributeSetId);
        }

        return $product;
    }
}
