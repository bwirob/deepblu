<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Marketplace\Controller\Adminhtml\Product\Admin;

use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class MassDelete extends \Magento\Catalog\Controller\Adminhtml\Product\MassDelete
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Netbaseteam\Marketplace\Model\ProductFactory $mpProductFactory,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Netbaseteam\Marketplace\Helper\Data $marketplaceHelperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $contextInterface,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency
    ) {
        $this->mpProductFactory = $mpProductFactory;
        $this->sellerdataFactory = $sellerdataFactory;
        $this->_marketplaceHelperData  = $marketplaceHelperData;
        $this->storeManager = $storeManager;
        $this->context = $contextInterface;
        $this->localeCurrency = $localeCurrency;
        parent::__construct($context, $productBuilder, $filter, $collectionFactory);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $mpProductCollection = $this->mpProductFactory->create()->getCollection();

        $productDeleted = 0;
        foreach ($collection->getItems() as $product) {
            $id = $product->getEntityId();
            $product->delete();

            if($id) {
                $mpProductCollection->addFieldToFilter('product_id', $id);
                $mpId = '';
                foreach ($mpProductCollection as $item) {
                    $mpId = $item->getId();
                }

                if ($mpId) {
                    $mpModel = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Product')->load($mpId);
                    $mpModel->delete();

                    $this->sendMail($mpModel,$product);
                }
            }

            $productDeleted++;
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $productDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('catalog/*/index');
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
            $sellerId = isset($data1['seller_id']) ? $data1['seller_id'] : '';
            if($sellerId) {
                $sellerData = $this->sellerdataFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);
                foreach ($sellerData as $item) {
                    $shopName = $item->getShopTitle();
                }
            } 
            
        }

        if($data2) {
            $productName = isset($data2['name']) ? $data2['name'] : '';
            $productSku = isset($data2['sku']) ? $data2['sku'] : '';
            $productPrice = isset($data2['price']) ? $data2['price'] : '';
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

        $emailTempVariables['templateSubject'] = "DemoShop";
        $emailTempVariables['message'] = __("I would like to inform you that your item $productName has been deleted.");
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
