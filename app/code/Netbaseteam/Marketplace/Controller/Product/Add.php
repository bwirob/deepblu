<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;

/**
 * Webkul Marketplace Product Add Controller.
 */
class Add extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Array of actions which can be processed without secret key validation.
     *
     * @var array
     */
    protected $_publicActions = ['edit'];

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param Context                                       $context
     * @param \Magento\Framework\View\Result\PageFactory    $resultPageFactory
     * @param \Magento\Customer\Model\Session               $customerSession
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context
        );
        $this->_resultPageFactory = $resultPageFactory;
        $this->productFactory = $productFactory;
        $this->_registry = $registry;
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get(
            'Magento\Customer\Model\Url'
        )->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Seller Product Add Action.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        if ($isPartner) {
            try {
                $set = $this->getRequest()->getParam('set');
                $type = $this->getRequest()->getParam('type');
                if (isset($set) && isset($type)) {
                    
                    /** @var $product \Magento\Catalog\Model\Product */
                    $product = $this->productFactory->create();
                    $product->setAttributeSetId($set);

                    $this->_registry->register('product_type', $type);
                    $this->_registry->register('current_product', $product);

                    /** @var \Magento\Framework\View\Result\Page resultPage */
                    $resultPage = $this->_resultPageFactory->create();
                    $resultPage->getConfig()->getTitle()->set(
                        __('Add Product')
                    );
                    return $resultPage;

                } else {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/new',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/new',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/registry',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
