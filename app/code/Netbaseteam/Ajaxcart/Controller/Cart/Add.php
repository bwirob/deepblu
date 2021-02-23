<?php

namespace Netbaseteam\Ajaxcart\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Netbaseteam\Ajaxcart\Helper\Data as AjaxcartData;

class Add extends \Magento\Checkout\Controller\Cart
{
    /**
     * @var Ajaxcart Data
     */
    protected $_ajaxcartData;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_itemFactory;

    protected $_wishlistProvider;

    protected $_resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        AjaxcartData $ajaxcartData,
        \Magento\Framework\Registry $registry,
        ProductRepositoryInterface $productRepository,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->productRepository = $productRepository;
        $this->_ajaxcartData = $ajaxcartData;
        $this->_coreRegistry = $registry;
        $this->_itemFactory = $itemFactory;
        $this->_wishlistProvider = $wishlistProvider;
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if ($this->_ajaxcartData->getConfigEnabled()&&isset($params['ajax'])) {
            $json_encode = [];
            $params = $this->getRequest()->getParams();
            if (isset($params['ajax']) && $params['ajax']) {
                try {
                    if (isset($params['qty'])) {
                        $filter = new \Zend_Filter_LocalizedToNormalized(
                            ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                        );
                        $params['qty'] = $filter->filter($params['qty']);
                    }

                    $product = $this->_initProduct();
                    $related = $this->getRequest()->getParam('related_product');

                    /**
                     * Check product availability
                     */
                    if (!$product) {
                        $json_encode["error"] = 1;
                        $json_encode["error_msg"] = __("The product is not available");
                        $result = $this->_resultJsonFactory->create();
                        return $result->setData($json_encode);
                    }

                    /* return options popup content when product type is grouped */

                    if ($this->_ajaxcartData->checkVerson()) {
                        $productConfig = $product->getTypeId() != 'downloadable'&&$product->getHasOptions()
                            || ($product->getTypeId() == 'grouped' && !isset($params['super_group']))
                            || ($product->getTypeId() == 'configurable' && !isset($params['super_attribute']))
                            || $product->getTypeId() == 'bundle';
                    } else {
                        $productConfig = $product->getHasOptions()
                            || ($product->getTypeId() == 'grouped' && !isset($params['super_group']))
                            || ($product->getTypeId() == 'configurable' && !isset($params['super_attribute']))
                            || $product->getTypeId() == 'bundle';
                    }

                    if ($params['utype'] != "detail-add") {
                        if ($productConfig) {
                            $this->_coreRegistry->register('product', $product);
                            $this->_coreRegistry->register('current_product', $product);

                            $json_encode["popup_option"] = 1;

                            $htmlPopup = $this->_ajaxcartData->getPopupOptionHtml($product);
                            $json_encode['html_popup_option'] = $htmlPopup;

                            if (isset($params['item'])) {
                                $json_encode['item'] = $params['item'];
                            }

                            $result = $this->_resultJsonFactory->create();
                            return $result->setData($json_encode);
                        }
                    }

                    $this->cart->addProduct($product, $params);
                    if (!empty($related)) {
                        $this->cart->addProductsByIds(explode(',', $related));
                    }

                    $this->cart->save();

//                    if (isset($params['item'])) {
//                        $item = $this->_itemFactory->create()->load($params['item']);
//                        $wishlist = $this->_wishlistProvider->getWishlist($item->getWishlistId());
//
//                        $item->delete();
//                        $wishlist->save();
//                        $json_encode["item"] = $params['item'];
//                    }

                    /**
                     * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
                     */

                    if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                        if (!$this->cart->getQuote()->getHasError()) {
                            $message = __(
                                'You added %1 to your shopping cart.',
                                $product->getName()
                            );
                            $this->_coreRegistry->register('product', $product);
                            $this->_coreRegistry->register('current_product', $product);
                            //$this->messageManager->addSuccessMessage($message);
                            $json_encode["success_msg"] = $message;
                            $json_encode["error"] = 0;
                            $htmlPopup = $this->_ajaxcartData->getSuccessHtml($product);
                            $json_encode['html_popup'] = $htmlPopup;
                        }
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    if ($this->_checkoutSession->getUseNotice(true)) {
                        $this->messageManager->addNotice(
                            $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                        );
                        $json_encode["error_msg"] = $e->getMessage();
                    } else {
                        $messages = array_unique(explode("\n", $e->getMessage()));
                        foreach ($messages as $message) {
                            $json_encode["error_msg"] = $message;
                        }
                    }
                    $json_encode["error"] = 1;
                } catch (\Exception $e) {
                    $json_encode["error_msg"] = $e->getMessage();
                    $this->messageManager->addError(__($json_encode["error_msg"]));
                }
            }
            $result = $this->_resultJsonFactory->create();
            return $result->setData($json_encode);
        } else {
            if (!$this->_formKeyValidator->validate($this->getRequest())) {
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }

            $params = $this->getRequest()->getParams();
            try {
                if (isset($params['qty'])) {
                    $filter = new \Zend_Filter_LocalizedToNormalized(
                        ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }

                $product = $this->_initProduct();
                $related = $this->getRequest()->getParam('related_product');

                /**
                 * Check product availability
                 */
                if (!$product) {
                    return $this->goBack();
                }

                $this->cart->addProduct($product, $params);
                if (!empty($related)) {
                    $this->cart->addProductsByIds(explode(',', $related));
                }

                $this->cart->save();

                /**
                 * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
                 */
                $this->_eventManager->dispatch(
                    'checkout_cart_add_product_complete',
                    ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                );

                if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                    if (!$this->cart->getQuote()->getHasError()) {
                        $message = __(
                            'You added %1 to your shopping cart.',
                            $product->getName()
                        );
                        $this->messageManager->addSuccessMessage($message);
                    }
                    return $this->goBack(null, $product);
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_checkoutSession->getUseNotice(true)) {
                    $this->messageManager->addNotice(
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                    );
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $this->messageManager->addError(
                            $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
                        );
                    }
                }

                $url = $this->_checkoutSession->getRedirectUrl(true);

                if (!$url) {
                    $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                    $url = $this->_redirect->getRedirectUrl($cartUrl);
                }

                return $this->goBack($url);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                return $this->goBack();
            }
        }
    }

    protected function goBack($backUrl = null, $product = null)
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}
