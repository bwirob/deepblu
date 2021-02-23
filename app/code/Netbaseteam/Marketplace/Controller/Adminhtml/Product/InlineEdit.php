<?php

namespace Netbaseteam\Marketplace\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Netbaseteam_Marketplace::marketplace_product';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;


    /**
     * @var \Netbaseteam\Marketplace\Helper\Data
     */
    protected $_helper;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Netbaseteam\Marketplace\Helper\Data $marketplaceHelperData,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $contextInterface,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Netbaseteam\Marketplace\Helper\Data $helper
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productFactory = $productFactory;
        $this->_marketplaceHelperData  = $marketplaceHelperData;
        $this->sellerdataFactory = $sellerdataFactory;
        $this->storeManager = $storeManager;
        $this->context = $contextInterface;
        $this->localeCurrency = $localeCurrency;
        $this->_helper = $helper;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        if ($this->_helper->releaseLimit()) {
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            $postItems = $this->getRequest()->getParam('items', []);

            if (!($this->getRequest()->getParam('isAjax'))) {
                return $resultJson->setData([
                    'messages' => [__('Please correct the data sent.')],
                    'error' => true,
                ]);
            }

            if ($postItems) {

                $id = 0;
                foreach ($postItems as $item) {
                    $id = $item['id'];
                    $status = $item['status'];
                }

                if ($id) {
                    $model = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Product')->load($id);

                    $model->setStatus($status);
                    $model->save();

                    $productModel = $this->productFactory->create()->load($model->getProductId());
                    $productModel->setStatus($status);
                    $productModel->save();
                    $this->sendMail($model,$productModel);

                }

                return $resultJson->setData([
                    'messages' => [__('Product status has been updated.')],
                    'error' => false
                ]);
            }
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
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
            $emailTempVariables['header'] = __("Congratulations! Your item $productName on DemoShop has been approved. You can view your item here:");
            $emailTempVariables['pro_link'] = __("$productLink");
        } else {
            $emailTempVariables['header'] = __("I would like to inform you that your item $productName has been disapproved.");
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

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
