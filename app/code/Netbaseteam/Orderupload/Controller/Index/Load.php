<?php

namespace Netbaseteam\Orderupload\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Load extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_objectManagerr;
	protected $_dataHelper;
	protected $_resultJsonFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Netbaseteam\Orderupload\Helper\Data $dataHelper,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_dataHelper 	   = $dataHelper;
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
		$uploadHelper = $this->_objectManagerr->get('\Netbaseteam\Orderupload\Helper\Data');
		$pid = $this->getRequest()->getParam('p');
		
		$session_id = session_id();
		if(empty($session_id)) session_start();
		$output_dir = $uploadHelper->getBaseDir()."/".$pid."/";
		$jsonFile = $output_dir.$session_id.'.json';
		if (!file_exists($jsonFile)){
			return;
		}
		
		$storeManager = $this->_objectManagerr->get('\Magento\Store\Model\StoreManagerInterface');
		$img_path_url = $storeManager->getStore()->getBaseUrl()."pub/media/Orderupload";
		
        $dir = $this->_dataHelper->getBaseDir()."/".$pid."/";
		$ret= array();
		
		if (file_exists($dir)){
			$files = scandir($dir);
			
			foreach($files as $file)
			{
				$str 	= file_get_contents($jsonFile);
				$rows 	= json_decode($str, true);
				for($i=0; $i < count($rows); $i++){
					foreach($rows[$i] as $row) {
						if ($pid == $row["parent_pid"] && $pid."/".$file == $row["file"]){
							
							$ext = explode(".", $file);
							if($file == "." || $file == ".." || $ext[1] == "json")
								continue;
							
							$filePathSize = $dir."/".$file;
							$filePath = $img_path_url."/".$pid."/".$file;
							
							$details = array();
							$details['name']=$file;
							$details['path']=$filePath;
							$details['size']=filesize($filePathSize);
							$details['comment']=$row["comment"];
							$ret[] = $details;
							
						} else {
							break;
						}
					}
				}
			}
		}
		$result = $this->_resultJsonFactory->create();
        return $result->setData($ret);
    }
}
