<?php
namespace Netbaseteam\Shopbybrand\Controller\Index;
use Magento\Framework\View\Result\PageFactory;

class Request extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $_resultJsonFactory;
    protected $_layoutFactory;
   

    protected $_coreRegistry;
	
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_coreRegistry = $registry;
        $this->_layoutFactory = $layoutFactory;
        parent::__construct($context);
    }
	
    
    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        $json_encode = [];
        $params = $this->getRequest()->getParams();
        if (!empty($params['categoryId'])) {  
                $categoryId = (int)$params['categoryId'];
                $dataRequest = [
                    'categoryId'=>$categoryId,
                    'home'=>$params['home']
                ];
                $this->_coreRegistry->register('dataRequest', $dataRequest);
                $json_encode["brand_view"] = $this->getBrandViewHtml(); 
            
            return $result->setData($json_encode);
        }
        $json_encode = ['error'=>1];
        return $result->setData($json_encode);
        
    }

    public function getBrandViewHtml()
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('brand_request_view');
        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }
}
