<?php

namespace Netbaseteam\Marketplace\Plugin\Model;

class SaveProduct
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Netbaseteam\Marketplace\Model\ProductFactory $mpProductFactory,
        \Netbaseteam\Marketplace\Helper\Data $marketplaceHelperData,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $contextInterface,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Catalog\Model\Product\Action $action,
        \Magento\Backend\Model\Session $session
    )
    {
        $this->_objectManager = $objectManager;
        $this->productFactory = $productFactory;
        $this->_mpProductFactory = $mpProductFactory;
        $this->_marketplaceHelperData  = $marketplaceHelperData;
        $this->sellerdataFactory = $sellerdataFactory;
        $this->storeManager = $storeManager;
        $this->context = $contextInterface;
        $this->localeCurrency = $localeCurrency;
        $this->action = $action;
        $this->_session = $session;
    }

    public function afterSave(\Magento\Catalog\Model\Product $subject, $result)
    {
        if ($this->_session->getBreak()) {
            $this->_session->unsBreak();
            return $result;
        }
        $productId = $result['entity_id'];
        $proStatus = $result['status'];
        $mpProduct = $this->_mpProductFactory->create()->getCollection()->addFieldToFilter('product_id', $productId);

        $id = '';
        foreach ($mpProduct as $item) {
            $id = $item->getId();
        }
		
        if($productId) {
            $product = $this->productFactory->create()->load($productId);
            if ($result['store_id'] == $this->storeManager->getDefaultStoreView()->getStoreId() || $result['store_id'] == 0) {
                $product->setStoreId($this->storeManager->getDefaultStoreView()->getStoreId());
            }
			
			if($id) {
				$sellerProductColls = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Product')->load($id);
				$sellerProductColls->setStatus($proStatus);

				$sellerProductColls->save();

                $this->sendMail($sellerProductColls,$product);
			}
			
            $this->_session->setBreak(1);
            $product->save();
        }

        return $result;
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