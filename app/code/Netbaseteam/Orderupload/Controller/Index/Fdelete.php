<?php

namespace Netbaseteam\Orderupload\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Fdelete extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_objectManagerr;

    protected $_jsonHelper;

    protected $_resultJsonFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		$this->_jsonHelper = $jsonHelper;
		$this->_resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }
	
    /**
     * Default Orderupload Index page
     *
     * @return void
     */
    public function execute()
    {
		// server should keep session data for AT LEAST 3 hour
		ini_set('session.gc_maxlifetime', 10800);
		
		$session_id = session_id();
		if(empty($session_id)) session_start();
			
		$pid = $this->getRequest()->getParam('p'); 
		$uploadHelper = $this->_objectManagerr->get('\Netbaseteam\Orderupload\Helper\Data');
		$output_dir = $uploadHelper->getBaseDir()."/".$pid."/";

		$op = $this->getRequest()->getPostValue('op');
		$fileName = $this->getRequest()->getPostValue('name');

		if(isset($op) && $op == "delete" && isset($fileName)){
			//$fileName = $_POST['name'];
			
			/* delete json */
			$jsonFile = $output_dir.$session_id.'.json';
			
			/* delete image from checkout/cart */
			$page_delete = $this->getRequest()->getParam('page_delete'); 
			if(isset($page_delete) && $page_delete == "checkout"){
				$jsonFile = $this->getRequest()->getParam('json_file');
			}
			
			$content 	= array();
			$count = 0;
			if (file_exists($jsonFile)){
				$str 	= file_get_contents($jsonFile);
				$rows 	= json_decode($str, true);
				for($i=0; $i < count($rows); $i++){
					/* \zend_debug::dump($row->file);
					\zend_debug::dump($pid."/".$fileName); */
					$tmp = array();
					foreach($rows[$i] as $row) {
						if ($row["file"] != $pid."/".$fileName){
							$tmp[] = $row;
							$count++;
						}
					}
					$content[] = $tmp;
					
				}
				file_put_contents($jsonFile, json_encode($content));
			}
			/* end delete json */
			
			$fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files	
			$filePath = $output_dir. $fileName;
			if (file_exists($filePath)) 
			{
				unlink($filePath);
			}
			$ret = array();
			if(isset($page_delete) && $page_delete == "checkout"){
				$ret['error'] = 0;
				$ret['count_items'] = $count;
				
				
				$result = $this->_resultJsonFactory->create();
            	return $result->setData($ret);			
			}else{
				$ret['error'] = 0;
				$result = $this->_resultJsonFactory->create();
            	return $result->setData($ret);
			}
			
		}
    }
}
