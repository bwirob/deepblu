<?php

namespace Netbaseteam\Marketplace\Controller\Transaction;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\Controller\ResultFactory;

class ListTransaction extends \Magento\Framework\App\Action\Action
{
    protected $customerSession;

    protected $pageFactory;

    protected $factory;

    protected $_helper;

    protected $_product;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\View\Element\UiComponentFactory $factory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        \Netbaseteam\Marketplace\Block\Catalog\Product $product,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->pageFactory = $pageFactory;
        $this->factory = $factory;
        $this->customerSession = $customerSession;
        $this->_registry = $registry;
        $this->_helper = $helper;
        $this->_product = $product;
        $this->_messageManager = $messageManager;
        return parent::__construct($context);
    }

    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            $helper = $this->_helper;
            $isPartner = $helper->getSellerId();
            if ($isPartner) {
                $this->_registry->register('sellerId', $this->customerSession->getCustomer()->getId());
                $isAjax = $this->getRequest()->isAjax();
                if ($isAjax) {
                    $component = $this->factory->create($this->_request->getParam('namespace'));
                    $this->prepareComponent($component);
                    $this->_response->appendBody((string) $component->render());
                } else {
                    $resultPage = $this->pageFactory->create();
                    return $resultPage;
                }
            } else {
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('marketplace/account/registry');
                return $resultRedirect;
            }
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
    }

    protected function prepareComponent(UiComponentInterface $component)
    {
        foreach ($component->getChildComponents() as $child) {
            $this->prepareComponent($child);
        }
        $component->prepare();
    }
}

