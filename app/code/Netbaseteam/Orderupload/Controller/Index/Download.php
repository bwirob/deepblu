<?php

namespace Netbaseteam\Orderupload\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Download extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_objectManagerr;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
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

		$pid = $this->getRequest()->getParam('p'); 
		$uploadHelper = $this->_objectManagerr->get('\Netbaseteam\Orderupload\Helper\Data');
		$output_dir = $uploadHelper->getBaseDir()."/".$pid."/";
		$fileName = $this->getRequest()->getParam('filename');
		if(isset($fileName)){
			$fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files
			$file = $output_dir.$fileName;
			$file = str_replace("..","",$file);
			if (file_exists($file)) {
				$fileName =str_replace(" ","",$fileName);
				header('Content-Description: File Transfer');
				header('Content-Disposition: attachment; filename='.$fileName);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				return;
			}
		}
    }
}
