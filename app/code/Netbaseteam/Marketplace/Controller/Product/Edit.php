<?php

namespace Netbaseteam\Marketplace\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;


class Edit extends Action
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
     * @param Netbaseteam\Marketplace\Controller\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\PageFactory    $resultPageFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Netbaseteam\Marketplace\Controller\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context
        );
        $this->productBuilder = $productBuilder;
        $this->_resultPageFactory = $resultPageFactory;
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
        $loginUrl = $this->_objectManager->get('Magento\Customer\Model\Url')
            ->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Seller Product Edit Action.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $productId = (int) $this->getRequest()->getParam('id');

        $isPartner = $helper->getSellerId();
        $isCorrectSeller = $helper->isCorrectSeller($productId);

        if ($isPartner && $isCorrectSeller) {
            $helper = $this->_objectManager->get(
                'Netbaseteam\Marketplace\Helper\Data'
            );
            $product = $this->productBuilder->build(
                $this->getRequest()->getParams(),
                $helper->getCurrentStoreId()
            );

            $model = $this->_objectManager->create('Magento\Catalog\Model\Product');

            if ($productId) {
                $model->load($productId);
                $this->_registry->register('product_edit', $model);

                $resultPage = $this->_resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set(
                    __('Edit Product')
                );

                return $resultPage;
            } else {
                $this->messageManager->addError(__('This product no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/',
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
