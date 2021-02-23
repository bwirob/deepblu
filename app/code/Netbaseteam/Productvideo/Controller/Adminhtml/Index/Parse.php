<?php

namespace Netbaseteam\Productvideo\Controller\Adminhtml\Index;

use Magento\Framework\View\Result\PageFactory;

class Parse extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    protected $_resultJsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
	
    /**
     * Default Productvideo Index page
     *
     * @return void
     */
    public function execute()
    {
		$data = array();
		$uploadHelper = $this->_objectManager->get('\Netbaseteam\Productvideo\Helper\Data');
		$path = $uploadHelper->getBaseDir();
		$params = $this->getRequest()->getParams();
		$data = $uploadHelper->getVideoInforFromURL(trim($params["vurl"]));
        $result = $this->_resultJsonFactory->create();
        return $result->setData($data);
    }
}
