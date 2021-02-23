<?php

namespace Netbaseteam\Faq\Block\Faq;

class Contactform extends \Magento\Framework\View\Element\Template
{

    
    protected $_coreRegistry = null;

    protected $_dataHelper;

    protected $_faqCollection;

    protected $_resultJsonFactory;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Netbaseteam\Faq\Helper\Data $dataHelper,
        \Netbaseteam\Faq\Model\FaqFactory $faqFactory,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory,
        array $data = []
    ) {

        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->_dataHelper = $dataHelper;
        $this->_faqCollection = $faqFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $data);
    }

    
     public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getStoreId(){

        return  $this->_dataHelper->getStoreviewId();
    }

    public function getFormAction()
    {
        return $this->getUrl('faq/index/contact', ['_secure' => true]);
    }

    public function getProductId(){
        $product = $this->_coreRegistry->registry('current_product');
        $productId =  $product->getId();
        return $productId;
    }

    

    public function getCallBackUrl(){
        $baseUrl = $this->_dataHelper->getBaseUrls();
        $callBackUrl = $baseUrl.'faq';
        return $callBackUrl;
    }
    

   
   
    
}
