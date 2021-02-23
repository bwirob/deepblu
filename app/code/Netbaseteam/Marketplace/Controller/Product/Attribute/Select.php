<?php

namespace Netbaseteam\Marketplace\Controller\Product\Attribute;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Select extends Action
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

    public function execute()
    {
        if($this->getRequest()->getParam('isAjax')){
            $content = $this->_view->getLayout()->createBlock("Netbaseteam\Marketplace\Block\Catalog\Product\Attribute")
                ->setTemplate("Netbaseteam_Marketplace::catalog/product/edit/attribute/items/new.phtml");

            $result = ['content' => $content->toHtml()];
        } else {
            $result = ['error' => 1];
        }

        $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
        
    }
}
