<?php

namespace Netbaseteam\Faq\Controller\Request;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_dataHelper;
    protected $_coreRegistry;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Netbaseteam\Faq\Helper\Data $dataHelper,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }
	
    /**
     * Default FAQ Index page
     *
     * @return void
     */
    public function execute()
    {
        $requestType = $this->getRequest()->getParam('request_type');
        if ($requestType=='tag'){
            $q = $this->getRequest()->getParam('tag');
            $this->_coreRegistry->register('tag', $q);
            $htmlPopup = $this->_dataHelper->getLisfFaqByTagHtml();
            $result = $this->_resultJsonFactory->create();
            return $result->setData($htmlPopup);
            
        }elseif ($requestType == 'search'){
            $q = $this->getRequest()->getParam('q_str');
            $this->_coreRegistry->register('search', $q);
            $htmlPopup = $this->_dataHelper->getLisfFaqBySearchHtml();
            $result = $this->_resultJsonFactory->create();
            return $result->setData($htmlPopup);
        }elseif ($requestType == 'category'){
            $categoryId = (int)$this->getRequest()->getParam('category');
            $this->_coreRegistry->register('category_id', $categoryId);
            $htmlPopup = $this->_dataHelper->getLisfFaqByCategoryHtml();
            $result = $this->_resultJsonFactory->create();
            return $result->setData($htmlPopup);
        }
        
    }
}
