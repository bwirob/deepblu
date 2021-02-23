<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Opc\Controller\Giftwrap;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class Process extends \Magento\Framework\App\Action\Action
{
    const GIFT_WRAP_TYPE = 'cmsmart_opc/gift_wrap/type';
    const GIFT_WRAP_AMOUNT = 'cmsmart_opc/gift_wrap/amount';
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var CartTotalRepositoryInterface
     */
    protected $cartTotalRepository;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Quote\Model\Quote\Address\Total $total,
        CartTotalRepositoryInterface $cartTotalRepository,
        CheckoutSession $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->total = $total;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfiguration = $scopeConfiguration;
        $this->storeManager = $storeManager;
		$this->_resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $isChecked = $this->getRequest()->getParam('isChecked');
        $cartsess = $this->_objectManager->get('Magento\Checkout\Model\Session');

        $quote = $cartsess->getQuote();
        if ($isChecked == 'false') {
            $giftWrapAmount = 0;
        } else {
            $store = $this->getStoreId();
            $giftWrapType = $this->scopeConfiguration->getValue(self::GIFT_WRAP_TYPE, ScopeInterface::SCOPE_STORE, $store);
            $giftWrapAmount = $this->scopeConfiguration->getValue(self::GIFT_WRAP_AMOUNT, ScopeInterface::SCOPE_STORE, $store);

            if ($giftWrapType == 2) {
                $giftWrapAmount = $giftWrapAmount * $quote->getItemsQty();
            }
        }


        $quote->setGiftwrapAmount($giftWrapAmount);
        $quote->save();

        $cartsess->getQuote()->collectTotals()->save();

		$result = $this->_resultJsonFactory->create();
        return $result->setData($giftWrapAmount);
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }
}
