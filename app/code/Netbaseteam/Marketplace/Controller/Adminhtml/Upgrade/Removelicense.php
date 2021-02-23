<?php

namespace Netbaseteam\Marketplace\Controller\Adminhtml\Upgrade;

use Magento\Framework\View\Result\PageFactory;

class Removelicense extends \Magento\Framework\App\Action\Action {
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_resultJsonFactory;
    protected $_helper;
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    public function execute() {
            $this->_helper->_deleteContent2File();
            $this->_helper->_writeContent2File("");
    }
}