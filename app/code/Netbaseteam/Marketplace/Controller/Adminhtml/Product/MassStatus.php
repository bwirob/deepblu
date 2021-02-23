<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Netbaseteam\Marketplace\Controller\Adminhtml\AbstractMassAction;
use Netbaseteam\Marketplace\Model\ResourceModel\Product\CollectionFactory;
use Netbaseteam\Marketplace\Model\ProductFactory;

/**
 * Class MassDelete
 */
class MassStatus extends AbstractMassAction
{
    const ADMIN_RESOURCE = 'Netbaseteam_Marketplace::marketplace_product';

    /**
     * @var \Netbaseteam\Marketplace\Helper\Data
     */
    protected $_helper;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        ProductFactory $manageFactory,
        \Netbaseteam\Marketplace\Helper\Data $marketplaceHelperData,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $contextInterface,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Netbaseteam\Marketplace\Helper\Data $helper
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->productFactory = $productFactory;
        $this->model = $manageFactory;
        $this->_marketplaceHelperData  = $marketplaceHelperData;
        $this->sellerdataFactory = $sellerdataFactory;
        $this->storeManager = $storeManager;
        $this->context = $contextInterface;
        $this->localeCurrency = $localeCurrency;
        $this->_helper = $helper;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction($collection)
    {
        if ($this->_helper->releaseLimit()) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $status = (int) $this->getRequest()->getParam('status');

            $itemsSelected = 0;
            foreach ($collection as $item) {
                $mpModel = $this->model->create()->load($item->getId());
                $mpModel->setStatus($status);
                $mpModel->save();
                $productModel = $this->productFactory->create()->load($item->getProductId());
                if ($status == 2) {
                    $productModel->setStatus(2);
                    $productModel->save();
                }
                $this->sendMail($mpModel,$productModel);
                $itemsSelected++;
            }

            if ($itemsSelected) {
                $this->messageManager->addSuccess(__('A total of %1 product(s) were updated.', $itemsSelected));
            } else {
                $this->messageManager->addErrorMessage('Something went wrong while update product');
            }

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->getComponentRefererUrl());

            return $resultRedirect;
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

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}