<?php

namespace Netbaseteam\Ajaxcart\Controller\Wishlist;

use Magento\Framework\App\Action;
use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Framework\Controller\ResultFactory;

class Cart extends \Magento\Wishlist\Controller\AbstractIndex
{
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Wishlist\Model\LocaleQuantityProcessor
     */
    protected $quantityProcessor;

    /**
     * @var \Magento\Wishlist\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;

    /**
     * @var \Magento\Wishlist\Model\Item\OptionFactory
     */
    private $optionFactory;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $helper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Ajaxcart Data
     */
    protected $_ajaxcartData;

    /**
     * @param Action\Context $context
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Wishlist\Model\LocaleQuantityProcessor $quantityProcessor
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Wishlist\Model\Item\OptionFactory $
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Wishlist\Helper\Data $helper
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Action\Context $context,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Wishlist\Model\LocaleQuantityProcessor $quantityProcessor,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Wishlist\Model\Item\OptionFactory $optionFactory,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Wishlist\Helper\Data $helper,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Ajaxcart\Helper\Data $ajaxcartData
    ) {

        $this->wishlistProvider = $wishlistProvider;
        $this->quantityProcessor = $quantityProcessor;
        $this->itemFactory = $itemFactory;
        $this->cart = $cart;
        $this->optionFactory = $optionFactory;
        $this->productHelper = $productHelper;
        $this->escaper = $escaper;
        $this->helper = $helper;
        $this->cartHelper = $cartHelper;
        $this->_ajaxcartData = $ajaxcartData;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Add product to shopping cart from wishlist action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
    	
        $params = $this->getRequest()->getParams();

        if(isset($params['isAjax']) && $params['isAjax']){
        	try {
				if (isset($params['qty'])) {
					$filter = new \Zend_Filter_LocalizedToNormalized(
						['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

				$itemID = $params['item'];

				$item = $this->itemFactory->create()->load($itemID);

				if (!$item->getId()) {
	                $resultRedirect->setPath('*/*');
	                return $resultRedirect;
            	}

            	$wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
	            if (!$wishlist) {
	                $resultRedirect->setPath('*/*');
	                return $resultRedirect;
	            }

	            $product = $item->getProduct();

	            if (!$product) {
	                $resultRedirect->setPath('*/*');
	                return $resultRedirect;
            	}

            	

            	$params['product'] = $product->getId();
            	$params['ajax'] = 1;
            	$params['utype'] = "option-product";

            	  $this->getResponse()->representJson(
                    $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($params)
                );

            	$this->_forward(
                    'add',
                    'cart',
                    'checkout',
                    $params);

                return;




			}catch (\Magento\Framework\Exception\LocalizedException $e) {
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
				$this->messageManager->addError( __($json_encode["error_msg"]));
			}	



        }

        
     }


}