<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Opc\Controller\Quote;

use Magento\Checkout\Model\Cart as CustomerCart;

class Update extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->cart = $cart;
        $this->_jsonHelper = $jsonHelper;

        parent::__construct($context);
    }

    /**
     * Delete shopping cart item action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $updateType = $this->getRequest()->getParam('updateType');


        switch ($updateType) {
            case 'delete':
                $this->deleteItem();
                break;
            case 'update':
                $this->updateItem();
                break;
            default:
                $this->updateItem();
        }

    }

    public function deleteItem() {
        $id = $this->getRequest()->getParam('itemId');
        $result = array();
        if ($id) {
            try {
                $this->cart->removeItem($id)->save();
                $result['error'] = 0;

            } catch (\Exception $e) {
                $result['error'] = 1;
            }

        }
        $totalQty = 0;
        $items = $this->cart->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            $totalQty = $item->getTotalQty();
        }

        if(!$totalQty) {
            $result['cartEmpty'] = 1;

        } else {
            $result['cartEmpty'] = 0;
        }

        $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
    }

    public function updateItem() {
        $id = $this->getRequest()->getParam('itemId');
        $qTy = $this->getRequest()->getParam('qty');
        $result = array();
        if ($id) {
            try {
                $currentQty['qty'] = $qTy;
                $cartData[$id] = $currentQty;

                $cartData = $this->cart->suggestItemsQty($cartData);
                $this->cart->updateItems($cartData)->save();

                $result = ['error' => 0];

            } catch (\Exception $e) {
                $result['error'] = 1;
            }

        }

        $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
    }
}
