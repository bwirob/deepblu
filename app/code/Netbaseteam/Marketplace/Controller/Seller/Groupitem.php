<?php

namespace Netbaseteam\Marketplace\Controller\Seller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Marketplace Seller Group Items controller.
 */
class Groupitem extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * Marketplace Seller's Profile Page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(
            __('Top Offers')
        );

        $productId = (int) $this->getRequest()->getParam('id');

        if ($productId) {
            $model = $this->_objectManager->create('Magento\Catalog\Model\Product');
            $model->load($productId);
            $this->_registry->register('current_product', $model);
        } else {
            $this->messageManager->addError(__('This product no longer exists.'));
        }

        return $resultPage;
    }
}
