<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckConditionObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Netbaseteam\Marketplace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Netbaseteam\Marketplace\Block\Catalog\Product
     */
    protected $_product;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * CheckConditionObserver constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Netbaseteam\Marketplace\Helper\Data $helper
     * @param \Netbaseteam\Marketplace\Block\Catalog\Product $product
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        \Netbaseteam\Marketplace\Block\Catalog\Product $product,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_request = $request;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->_helper = $helper;
        $this->_product = $product;
        $this->_messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $moduleName = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $action     = $this->_request->getActionName();
        $route      = $this->_request->getRouteName();
        if ($moduleName == 'marketplace' && $route == 'marketplace') {
            if (!$this->_helper->releaseLimit()) {
                $customerBeforeAuthUrl = $this->_url->getUrl();
                $observer->getControllerAction()->getResponse()->setRedirect($customerBeforeAuthUrl);
            }
        }
        $product = $this->_product->getProductCollection()->getData();
        if ($moduleName == 'marketplace' && $route == 'marketplace' && $controller == 'product' && $action == 'add') {
            if ($this->_helper->releaseLimit() == 'free' && count($product) >= 5) {
                $error = 'WW91IGNhbiBub3QgYWRkIG1vcmUgdGhhbiA1IHByb2R1Y3RzLiBJZiB5b3Ugc3RpbGwgd2FudCB0byBhZGQgbW9yZSB0aGFuIDUgcHJvZHVjdHMsIHlvdSBuZWVkIHRvIGJ1eSB0aGUgPGEgdGFyZ2V0PSdfYmxhbmsnIGhyZWY9J2h0dHBzOi8vY21zbWFydC5uZXQvbWFnZW50by0yLWV4dGVuc2lvbnMvbWFnZW50by1tdWx0aS12ZW5kb3InPmxpY2Vuc2U8L2E+IHZlcnNpb24u';
                $this->_messageManager->addError(base64_decode($error));
                $url = $this->_url->getUrl('marketplace/product/');
                $observer->getControllerAction()->getResponse()->setRedirect($url);
            }
        }
    }
}
