<?php

namespace Netbase\Product\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\LayoutFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	protected $_resultJsonFactory;
    protected $_layoutFactory;

   protected $_coreRegistry;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        LayoutFactory $layoutFactory,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_resultJsonFactory    = $resultJsonFactory;
        $this->_layoutFactory = $layoutFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
	
    /**
     * Default Product Index page
     *
     * @return void
     */
    public function execute()
    {
		$resultPage = $this->resultPageFactory->create();
		$result = $this->_resultJsonFactory->create();
		$json_encode = [];
		$params = $this->getRequest()->getParams();
        if (!empty($params['categoryId'])) { 
			if ($params['type'] == 'deals') {
                $categoryId = (int)$params['categoryId'];
                $dataRequest = [
                    'categoryId'=>$categoryId,
                    'home'=>$params['home']
                ];
                $this->_coreRegistry->register('dataRequest', $dataRequest);  
                $json_encode["cate_view"] = $this->getCategoryViewHtml(); 
            } 
            elseif ($params['type'] == 'bestseller') { 
                $categoryId = (int)$params['categoryId'];
                $dataRequest = [
                    'categoryId'=>$categoryId,
                    'home'=>$params['home']
                ];
                $this->_coreRegistry->register('dataRequest', $dataRequest); 
                $json_encode["cate_view"] = $this->getBestsellerViewHtml(); 
            }
            elseif ($params['type'] == 'newproduct') { 
                $categoryId = (int)$params['categoryId'];
                $dataRequest = [
                    'categoryId'=>$categoryId,
                    'home'=>$params['home']
                ]; 
                $this->_coreRegistry->register('dataRequest', $dataRequest); 
                $json_encode["cate_view"] = $this->getNewproductViewHtml(); 
            }
            elseif ($params['type'] == 'feature') {
                
                $categoryId = (int)$params['categoryId'];
                $dataRequest = [
                    'categoryId'=>$categoryId,
                    'home'=>$params['home']
                ];    
                $this->_coreRegistry->register('dataRequest', $dataRequest);
                $json_encode["cate_view"] = $this->getFeatureViewHtml(); 
            }

            return $result->setData($json_encode);
        }
        $json_encode = ['error'=>1];
        return $result->setData($json_encode);
    }

    public function getCategoryViewHtml()
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('cate_request_view');
        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }
    public function getBestsellerViewHtml()
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('bestseller_request_view');
        $layout->generateXml();
        $layout->generateElements(); 
        return $layout->getOutput();
    }
    public function getNewproductViewHtml()
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('new_request_view');
        $layout->generateXml();
        $layout->generateElements(); 
        return $layout->getOutput();
    }
    public function getFeatureViewHtml()
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('feature_request_view');
        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }
}
