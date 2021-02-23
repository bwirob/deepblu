<?php

namespace Netbaseteam\Orderupload\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Updcproductdetail extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_objectManagerr;
    protected $_resultJsonFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
		$this->_objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }
	
    /**
     * Default Orderupload Index page
     *
     * @return void
     */
    public function execute()
    {
		$pid = $this->getRequest()->getParam('parent_id'); 
		$cid = $this->getRequest()->getParam('child_id'); 
		$comment = $this->getRequest()->getParam('msg'); 
		$file = $this->getRequest()->getParam('file'); 
		/* \zend_debug::dump($pid);
		\zend_debug::dump($cid);
		\zend_debug::dump($comment);
		\zend_debug::dump($file);
		die; */
		
		/* if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name'])){ */
			
			$jsonFile = $this->getRequest()->getParam('json_file');
			$content 	= array();
			if (file_exists($jsonFile)){
				$str 	= file_get_contents($jsonFile);
				$rows 	= json_decode($str, true);
				for($i=0; $i < count($rows); $i++){
					$tmp = array();$tmp1 = array();
					foreach($rows[$i] as $row) {
						if ($pid == $row["parent_pid"] && $file == $row["file"]){
							$tmp["order_id"] = $row["order_id"];
							$tmp["parent_pid"] = $row["parent_pid"];
							$tmp["file"] = $row["file"];
							$tmp["child_pid"] = $row["child_pid"];
							$tmp["comment"] = $comment;
							$tmp1[] = $tmp;
						} else {
							$tmp1[] = $row;
						}
					}
					$content[] = $tmp1;
				}
				file_put_contents($jsonFile, json_encode($content));
			}

			$json_encode = array();
			$json_encode["message"] = __('Update file successfully...');
			$result = $this->_resultJsonFactory->create();
        	return $result->setData($json_encode);
		/* } */
    }
}
