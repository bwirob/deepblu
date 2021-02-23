<?php

namespace Netbaseteam\Marketplace\Controller\Seller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Marketplace Seller Profile controller.
 */
class Profile extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    protected $_jsonHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_jsonHelper = $jsonHelper;
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
            __('Marketplace Seller Profile')
        );

        if($this->getRequest()->getParam('isAjax')){
            if ($this->getRequest()->getParam('about')) {
                $content = $this->_view->getLayout()->createBlock("Netbaseteam\Marketplace\Block\Seller\Profile")
                    ->setTemplate("Netbaseteam_Marketplace::seller/about.phtml");
            } elseif ($this->getRequest()->getParam('product')) {
                $content = $this->_view->getLayout()->createBlock("Netbaseteam\Marketplace\Block\Seller\Profile\ListProduct")
                    ->setTemplate("Netbaseteam_Marketplace::seller/product.phtml");
            }
            elseif ($this->getRequest()->getParam('event')) {
                $content = $this->_view->getLayout()->createBlock("Netbaseteam\Marketplace\Block\Seller\Profile")
                    ->setTemplate("Netbaseteam_Marketplace::seller/event.phtml");
            }
            else {
                $content = $this->_view->getLayout()->createBlock("Netbaseteam\Marketplace\Block\Seller\Profile\ListProduct")
                    ->setTemplate("Netbaseteam_Marketplace::seller/homepage.phtml");
            }

            $result = ['content' => $content->toHtml()];
            $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
        } else {
            return $resultPage;
        }
    }
}
