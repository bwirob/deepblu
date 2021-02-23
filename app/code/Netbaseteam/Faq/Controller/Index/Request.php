<?php
namespace Netbaseteam\Faq\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Request extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
	
    
    public function execute()
    {
        
    }
}
