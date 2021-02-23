<?php

namespace Netbase\Product\Controller\Adminhtml\Index;

use Magento\Framework\View\Result\PageFactory;

class Deletecontent extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
	
    /**
     * Default Productvideo Index page
     *
     * @return void
     */
    public function execute()
    {
		$ret = array(); 
		$section = 0; $alias_id = 0; $typevalue_id = 0;
		$params = $this->getRequest()->getParams();
		$section = trim($params['section']);
		if($section == "") {
			die("Please set data");
		}
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('netbase_product_homecontent');

		$delete = "DELETE  FROM " . $tableName. " WHERE section = '".$section."'";
		$connection->query($delete);
				
		$ret["message"] = "The data has been deleted successfully";					
		echo json_encode($ret);	
		exit();
    }
}
