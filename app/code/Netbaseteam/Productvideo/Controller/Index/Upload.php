<?php

namespace Netbaseteam\Productvideo\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Upload extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
	protected $_resultJsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
	
    /**
     * Default Productvideo Index page
     *
     * @return void
     */
    public function execute()
    {
		$res = array(); $data = array();
		$result = true;
		
		$uploadHelper = $this->_objectManager->get('\Netbaseteam\Productvideo\Helper\Data');
		$params = $this->getRequest()->getParams();

		$uploader = $this->_objectManager->create(
			  'Magento\MediaStorage\Model\File\Uploader',
			  ['fileId' => 'file']
			);

		$fileData = $uploader->validateFile();
		
		if($params["type"] == \Netbaseteam\Productvideo\Model\Source\Type::local) {

			if(isset($fileData) && $fileData["size"] > $uploadHelper->getMaxVideoSize()*1024*1024){
				$res['message'] = __('You have upload too big file! You only upload file with max size '.$uploadHelper->getMaxVideoSize().' Mb');

				$resultData = $this->_resultJsonFactory->create();
        		return $resultData->setData($res); 
			}
		}

		$data["title"] = $params["video-name"];
		$data["url"] = $params["url"];
		$data["product_ids"] = $params["product_id"];
		$data["video_type"] = $params["type"];
		$data["content"] = $params["author-comment"];
		$data["created_at"] = date('Y-m-d H:i:s');
		$data["status"] = "1";
		$data["author_name"] = $params["author_name"];
		$data["author_email"] = $params["author_email"];
		$data["store_view"] = $uploadHelper->getCurrentStoreId();
		$vdInfo = $uploadHelper->getVideoInforFromURL($data["url"]);
		$data["vid"] = $vdInfo["video_id"];
		
		if(isset($fileData) && $params["type"] == \Netbaseteam\Productvideo\Model\Source\Type::local) {
			$path = $uploadHelper->getBaseDir();
			$ext = $uploadHelper->getExtensionFile($fileData["name"]);
			$client_up = "client_up_".date("Y")."_".date("m")."_".date("d")."_".date("H")."_".date("m")."_".date("s");
			$fNameUpload = $client_up.".".$ext;
			if(move_uploaded_file($fileData["tmp_name"], $path. "/" . $fNameUpload)){
				$data["url"] = $fNameUpload;
			} else {
				$result = false;
				$res['message'] = __('Error occurred with file upload!');            
			}   
		}
		
		$model = $this->_objectManager->create('Netbaseteam\Productvideo\Model\Productvideo');
		$model->addData($data);
		$model->save();
		
		if($result) {
			$res['message'] = __('Your video has uploaded successfully!');
		}

		$resultData = $this->_resultJsonFactory->create();
        return $resultData->setData($res); 
    }
}
