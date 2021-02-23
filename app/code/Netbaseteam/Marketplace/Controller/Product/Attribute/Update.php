<?php

namespace Netbaseteam\Marketplace\Controller\Product\Attribute;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Update extends Action
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
        
        if($this->getRequest()->getParam('data')){
            $content = $this->_view->getLayout()->createBlock("Netbaseteam\Marketplace\Block\Catalog\Product\Attribute")
                ->setTemplate("Netbaseteam_Marketplace::catalog/product/attribute/update.phtml");

            $result = ['content' => $content->toHtml()];
            $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
        } else {
            return $resultPage;
        }
    }
}
