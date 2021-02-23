<?php

namespace Cmsmart\Brandcategory\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Loadbrand extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	protected $_layoutFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_layoutFactory = $layoutFactory;
        parent::__construct($context);
    }
	
    /**
     * Default Brandcategory Index page
     *
     * @return void
     */
    public function execute()
    {
		$this->_view->loadLayout();
		
		$result = array();
		$result["template"] =$this->_layoutFactory->create()->createBlock('Cmsmart\Brandcategory\Block\Brandcategory')
						->setTemplate('Cmsmart_Brandcategory::brand_products.phtml')->toHtml();
        echo json_encode($result);
		exit();
    }
}
