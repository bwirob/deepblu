<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Payseller extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Netbaseteam_Marketplace::marketplace_order';

    protected $_storeManager;

    /**
     * @var \Netbaseteam\Marketplace\Helper\Data
     */
    protected $_helper;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Netbaseteam\Marketplace\Helper\Data $marketplaceHelperData,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Netbaseteam\Marketplace\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        $this->_marketplaceHelperData = $marketplaceHelperData;
        $this->orderFactory = $orderFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($this->_helper->releaseLimit()) {
            $entityId = $this->getRequest()->getParam('entity_id');
            $message = $this->getRequest()->getParam('send_message');
            $sellerAmount = $this->getRequest()->getParam('seller_amount');
            $commisionAmount = $this->getRequest()->getParam('commission');

            $mpOrder = '';
            if ($entityId) {
                $mpOrder =  $this->_objectManager->create('Netbaseteam\Marketplace\Model\Order')->load($entityId);
                try {
                    $sellerId = $mpOrder->getSellerId();

                    $seller = $this->_objectManager->create(
                        'Netbaseteam\Marketplace\Model\Seller'
                    )
                        ->getCollection()
                        ->addFieldToFilter(
                            'seller_id',
                            $sellerId
                        );

                    $sellerData = $this->_objectManager->create(
                        'Netbaseteam\Marketplace\Model\Sellerdata'
                    )
                        ->getCollection()
                        ->addFieldToFilter(
                            'seller_id',
                            $sellerId
                        );

                    $totalSellerAmount = 0;
                    $totalCommissionAmount = 0;
                    $sellerEntityId = 0;
                    foreach ($seller as $item) {
                        $totalSellerAmount = $item->getTotalSellerAmount();
                        $totalCommissionAmount = $item->getTotalCommissionAmount();
                        $sellerEntityId = $item->getId();
                    }
                    $totalSellerAmount = $totalSellerAmount + trim($sellerAmount,$this->getCurrencySymbol());
                    $totalCommissionAmount = $totalCommissionAmount + trim($commisionAmount,$this->getCurrencySymbol());

                    $sellerModel = $this->_objectManager->create(
                        'Netbaseteam\Marketplace\Model\Seller'
                    )->load($sellerEntityId);

                    $shopTitle = '';
                    foreach ($sellerData as $item) {
                        $shopTitle = $item->getShopTitle();
                    }

                    $transaction = $this->_objectManager->create(
                        'Netbaseteam\Marketplace\Model\Transaction'
                    );

                    $mpOrder->setPaidStatus('paid');
                    $mpOrder->save();

                    $sellerModel->setTotalSellerAmount($totalSellerAmount);
                    $sellerModel->setTotalCommissionAmount($totalCommissionAmount);
                    $sellerModel->save();

                    $transaction->setSellerId($sellerId);
                    $transaction->setShopTitle($shopTitle);
                    $transaction->setOrderId($mpOrder->getOrderId());
                    $transaction->setAmount(trim($sellerAmount,$this->getCurrencySymbol()));
                    $transaction->save();

                    $this->sendMail($mpOrder,$message);
                    $this->messageManager->addSuccess(__('Payment has been successfully done for this seller'));
                }catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while paying for seller.'));
                }
            }


            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }

    private function sendMail($data,$message)
    {
        $sellerAmount = $this->getRequest()->getParam('seller_amount');
        $commisionAmount = $this->getRequest()->getParam('commission');

        $helper = $this->_marketplaceHelperData;
        $sellerName = '';
        $sellerEmail = '';
        $sellerId = '';
        $orderId = '';

        if ($data) {
            $sellerId = $data['seller_id'];
            $orderId = $data['order_id'];
        }

        $order = $this->orderFactory->create()->load($orderId);
        $orderIncrement = $order->getIncrementId();

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

        $emailTempVariables['admin'] = $adminUsername;
        $emailTempVariables['header'] = $message;
        $emailTempVariables['order'] = $orderIncrement;
        $emailTempVariables['seller_amount'] = $sellerAmount;
        $emailTempVariables['commission_amount'] = $commisionAmount;

        $senderInfo = [
            'name' => $adminUsername,
            'email' => $adminEmail,
        ];
        $receiverInfo = [
            'name' => $sellerName,
            'email' => $sellerEmail,
        ];

        $helper->sendPaysellerMail(
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

    public function getCurrencySymbol() {
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $currency = $this->_objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode);
        return $currencySymbol = $currency->getCurrencySymbol();
    }
}
