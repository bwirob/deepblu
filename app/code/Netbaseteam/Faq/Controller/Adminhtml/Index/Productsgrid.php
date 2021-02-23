<?php

namespace Netbaseteam\Faq\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Productsgrid extends \Magento\Backend\App\Action
{

    protected $_resultLayoutFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }


    protected function _isAllowed()
    {
        return true;
    }

    
    public function execute()
    {
        
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('faq.edit.tab.productslist')
                     ->setInProducts($this->getRequest()->getPost('product_ids', null));

        return $resultLayout;
    }
}
