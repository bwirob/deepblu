<?php
/**
 * Copyright © 2015 Customer Paradigm. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Opc\Model\Customblock;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

class CustomBlockConfigProvider implements ConfigProviderInterface
{
    const XPATH_COMMENT_ACTIVE = 'cmsmart_opc/general/onepage_checkout_comments_enabled';
    const XPATH_DELIVERY_ACTIVE = 'cmsmart_opc/delivery_date/active';
    const XPATH_DELIVERY_FORMAT = 'cmsmart_opc/delivery_date/format';
    const XPATH_DELIVERY_DISABLED = 'cmsmart_opc/delivery_date/disabled';
    const XPATH_DELIVERY_HOURMIN = 'cmsmart_opc/delivery_date/hourMin';
    const XPATH_DELIVERY_HOURMAX = 'cmsmart_opc/delivery_date/hourMax';
    const GIFT_WRAP_TYPE = 'cmsmart_opc/gift_wrap/type';
    const GIFT_WRAP_AMOUNT = 'cmsmart_opc/gift_wrap/amount';
    const GIFT_WRAP_ENABLE = 'cmsmart_opc/gift_wrap/enable';
    const GOOGLE_SUGGEST_ENABLE = 'cmsmart_opc/google_suggest_address/enable';
    const GOOGLE_SUGGEST_API = 'cmsmart_opc/google_suggest_address/api_key';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfiguration;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfiguration = $scopeConfiguration;
    }

    /**
     * @return array() $showHide
     */
    public function getConfig()
    {
        $store = $this->getStoreId();
        /** @var array() $showHide */
        $showHide = [];
        /** @var boolean $enabled */
        $commentEnabled = $this->scopeConfiguration
            ->getValue(self::XPATH_COMMENT_ACTIVE, ScopeInterface::SCOPE_STORE);
        $showHide['show_hide_comment_block'] = ($commentEnabled) ? true:false;

        $deliveryEnabled = $this->scopeConfiguration
            ->getValue(self::XPATH_DELIVERY_ACTIVE, ScopeInterface::SCOPE_STORE);
        $showHide['show_hide_delivery_block'] = ($deliveryEnabled) ? true:false;


        $showHide['suggest_address'] = $this->scopeConfiguration->getValue(self::GOOGLE_SUGGEST_ENABLE, ScopeInterface::SCOPE_STORE, $store);
        $googleAPIKey = $this->scopeConfiguration->getValue(self::GOOGLE_SUGGEST_API, ScopeInterface::SCOPE_STORE, $store);
        if ($googleAPIKey) {
            $showHide['google_api_key'] = $googleAPIKey;
        } else {
            $showHide['google_api_key'] = 'AIzaSyB4-LJz5kbD48ZAki_EVYuZC3yTUpbCObM';
        }

        $active = $this->scopeConfiguration->getValue(self::XPATH_DELIVERY_ACTIVE, ScopeInterface::SCOPE_STORE, $store);
        $disabled = $this->scopeConfiguration->getValue(self::XPATH_DELIVERY_DISABLED, ScopeInterface::SCOPE_STORE, $store);
        $hourMin = $this->scopeConfiguration->getValue(self::XPATH_DELIVERY_HOURMIN, ScopeInterface::SCOPE_STORE, $store);
        $hourMax = $this->scopeConfiguration->getValue(self::XPATH_DELIVERY_HOURMAX, ScopeInterface::SCOPE_STORE, $store);
        $format = $this->scopeConfiguration->getValue(self::XPATH_DELIVERY_FORMAT, ScopeInterface::SCOPE_STORE, $store);
        $giftWrapEnable = $this->scopeConfiguration->getValue(self::GIFT_WRAP_ENABLE, ScopeInterface::SCOPE_STORE, $store);
        $giftWrapAmount = $this->scopeConfiguration->getValue(self::GIFT_WRAP_AMOUNT, ScopeInterface::SCOPE_STORE, $store);
        $giftWrapType = $this->scopeConfiguration->getValue(self::GIFT_WRAP_TYPE, ScopeInterface::SCOPE_STORE, $store);

        $giftWrapAmountFinal = $giftWrapAmount;
        if ($giftWrapType == 2) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $cartsess = $objectManager->get('Magento\Checkout\Model\Session');

            $quote = $cartsess->getQuote();
            $giftWrapAmountFinal = $giftWrapAmount * $quote->getItemsQty();
        }

        $noday = 0;
        if($disabled == -1) {
            $noday = 1;
        }

        $showHide['shipping']= [
                'delivery_date' => [
                    'active' => $active,
                    'format' => $format,
                    'disabled' => $disabled,
                    'noday' => $noday,
                    'hourMin' => $hourMin,
                    'hourMax' => $hourMax
                ]
        ];

        $showHide['enable_giftwrap'] = $giftWrapEnable;
        $showHide['giftwrap_amount'] = $giftWrapAmount;
        $showHide['giftwrap_amount_final'] = $giftWrapAmountFinal;
        $showHide['giftwrap_type'] = $giftWrapType;

        return $showHide;
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }
}