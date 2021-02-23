<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Marketplace\Controller\Adminhtml\Product\Admin;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class MassStatus extends \Magento\Catalog\Controller\Adminhtml\Product\MassStatus
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_productPriceIndexerProcessor;

    /**
     * MassActions filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Action\Context $context
     * @param Builder $productBuilder
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Product\Builder $productBuilder,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Netbaseteam\Marketplace\Model\ProductFactory $mpProductFactory,
        \Netbaseteam\Marketplace\Helper\Data $marketplaceHelperData,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $contextInterface,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency
    ) {
        $this->productFactory = $productFactory;
        $this->mpProductFactory = $mpProductFactory;
        $this->_marketplaceHelperData  = $marketplaceHelperData;
        $this->sellerdataFactory = $sellerdataFactory;
        $this->storeManager = $storeManager;
        $this->context = $contextInterface;
        $this->localeCurrency = $localeCurrency;
        parent::__construct($context, $productBuilder, $productPriceIndexerProcessor, $filter, $collectionFactory);
    }

    /**
     * Update product(s) status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $productIds = $collection->getAllIds();
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $status = (int) $this->getRequest()->getParam('status');
        $filters = (array)$this->getRequest()->getParam('filters', []);

        if (isset($filters['store_id'])) {
            $storeId = (int)$filters['store_id'];
        }

        try {
            $this->_validateMassStatus($productIds, $status);
            $this->_objectManager->get('Magento\Catalog\Model\Product\Action')
                ->updateAttributes($productIds, ['status' => $status], $storeId);
            $mpProductCollection = $this->mpProductFactory->create()->getCollection();
            foreach ($productIds as $item) {
                $mpProductCollection->addFieldToFilter('product_id', $item);
                $mpPid = '';
                foreach ($mpProductCollection as $value) {
                    $mpPid = $value->getId();
                }

                $productModel = $this->productFactory->create()->load($item);

                if ($mpPid) {
                    $mpProduct = $this->mpProductFactory->create()->load($mpPid);
                    $mpProduct->setStatus($status);
                    $mpProduct->save();
                    $this->sendMail($mpProduct,$productModel);
                }
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', count($productIds)));
            $this->_productPriceIndexerProcessor->reindexList($productIds);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the product(s) status.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('catalog/*/', ['store' => $storeId]);
    }

    private function sendMail($data1, $data2)
    {
        $helper = $this->_marketplaceHelperData;
        $sellerName = '';
        $sellerEmail = '';
        $sellerId = '';
        $shopName = '';
        $productName = '';
        $productSku = '';
        $productPrice = '';

        if ($data1) {
            $sellerId = $data1['seller_id'];
            $sellerData = $this->sellerdataFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);

            foreach ($sellerData as $item) {
                $shopName = $item->getShopTitle();
            }
        }

        if($data2) {
            $productName = $data2['name'];
            $productSku = $data2['sku'];
            $productPrice = $data2['price'];
            $productLink = $data2->getProductUrl();
        }

        $store = $this->storeManager->getStore(
            $this->context->getFilterParam('store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
        );

        $currency = $this->localeCurrency->getCurrency($store->getBaseCurrencyCode());
        $priceAmount = $currency->toCurrency(sprintf("%f", $productPrice));

        if ($sellerId) {
            $customer = $this->_objectManager->get(
                'Magento\Customer\Model\Customer'
            )->load($sellerId);

            $sellerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $sellerEmail = $customer->getEmail();
        }


        $emailTempVariables = [];
        $adminStoremail = $helper->getAdminEmailId();
        $adminEmail = $adminStoremail ?
            $adminStoremail : $helper->getDefaultTransEmailId();
        $adminUsername = 'Admin';

        $emailTempVariables['admin'] = $shopName;
        $emailTempVariables['templateSubject'] = "DemoShop";
        if ($data2['status'] == 1) {
            $emailTempVariables['message'] = __("Congratulations! Your item $productName on DemoShop has been approved. You can view your item here:");
            $emailTempVariables['pro_link'] = __("$productLink");
        } else {
            $emailTempVariables['message'] = __("I would like to inform you that your item $productName has been disapproved.");
        }
        $emailTempVariables['product_name'] = $productName;
        $emailTempVariables['sku'] = $productSku;
        $emailTempVariables['price'] = $priceAmount;

        $senderInfo = [
            'name' => $adminUsername,
            'email' => $adminEmail,
        ];
        $receiverInfo = [
            'name' => $sellerName,
            'email' => $sellerEmail,
        ];

        $helper->sendAdminProductMail(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo
        );
    }
}
