<?php

namespace Netbaseteam\Productvideo\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(Action\Context $context, PostDataProcessor $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Productvideo::save');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
    
        $data = $this->getRequest()->getPostValue();
		
        if ($data) {
            $data = $this->dataProcessor->filter($data);
			
			$uploadHelper = $this->_objectManager->get('\Netbaseteam\Productvideo\Helper\Data');
			$localFile = $uploadHelper->uploadImage('local_video');
	
			$model = $this->_objectManager->create('Netbaseteam\Productvideo\Model\Productvideo');

            $id = $this->getRequest()->getParam('productvideo_id');

            if ($id) {
                $model->load($id);	
            }
			
			if($data["video_type"] == \Netbaseteam\Productvideo\Model\Source\Type::local 
				&& $localFile == false){
					if($model->getUrl() == "") {
						$this->messageManager->addError("Please upload video file from local.");
						$this->_getSession()->setFormData($data);
						$this->_redirect('*/*/edit', ['productvideo_id' => $this->getRequest()->getParam('productvideo_id')]);
						return;
					}
			}
			
			if($data["video_type"] == \Netbaseteam\Productvideo\Model\Source\Type::url && $data["url"] == ""){
				$this->messageManager->addError("Please enter your video url.");
				$this->_getSession()->setFormData($data);
				$this->_redirect('*/*/edit', ['productvideo_id' => $this->getRequest()->getParam('productvideo_id')]);
				return;
			}
			
			if($data["product_ids"] == ""){
				$this->messageManager->addError("Please select products for this video in Products tab.");
				$this->_getSession()->setFormData($data);
				$this->_redirect('*/*/edit', ['productvideo_id' => $this->getRequest()->getParam('productvideo_id')]);
				return;
			}

			// set store
			if (isset($data['store_view'])) 
				$data['store_view'] = implode(",", $data['store_view']);
			
			if($data['store_view'] == "0") $data['store_view'] = "0,";
			
			$vdInfo = $uploadHelper->getVideoInforFromURL($data["url"]);
			$data["vid"] = $vdInfo["video_id"];
			
			$data["created_at"] = date('Y-m-d H:i:s');
			$data['url'] = trim($data['url']);

			if($data["status"] == \Netbaseteam\Productvideo\Model\Source\Status::active) {
				$data["published_at"] = date('Y-m-d H:i:s');
			}
			
			$data["title"] = $data["title_tmp"];
			
            // save image data and remove from data array
            if (isset($data['local_video'])) {
				$uploadHelper->_createProductvideoFolder();
                $local_videoData = $data['local_video'];
                unset($data['local_video']);
            } else {
                $local_videoData = array();
            }
			
			if (isset($data['thumb'])) {
				$uploadHelper->_createProductvideoFolder();
                $thumbData = $data['thumb'];
                unset($data['thumb']);
            } else {
                $thumbData = array();
            }
			
			if (isset($data['local'])) {
                $localData = $data['local'];
                unset($data['local']);
            } else {
                $localData = array();
            }

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['productvideo_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                if (isset($local_videoData['delete'])) {
					$file_delete = $uploadHelper->getBaseDir()."/".$model->getUrl();
                    $uploadHelper->removeImage($file_delete);
                    $model->setUrl(null);
                    $model->setTitle(null);
                }
                
                if ($localFile) {
                    $model->setUrl($localFile);
					if($data['title'] == "") $model->setTitle($localFile);
                }
				
				if (isset($localData['delete']) && isset($localData['value'])) {
					$file_delete = $uploadHelper->getBaseDir()."/".$localData['value'];
					$uploadHelper->removeImage($file_delete);
                    $model->setThumb(null);
					$model->setTitle(null);
					$model->setUrl(null);
                }
				
				if (isset($thumbData['delete']) && isset($thumbData['value'])) {
					$file_delete = $uploadHelper->getBaseDir()."/".$thumbData['value'];
					$uploadHelper->removeImage($file_delete);
                    $model->setThumb(null);
                }
                
                $thumbFile = $uploadHelper->uploadImage('thumb');
                if ($thumbFile) {
                    $model->setThumb($thumbFile);
                } elseif($data['img_thumb'] != "") {
					$model->setThumb($data['img_thumb']);
				}
                
                $model->save();
				
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                
				if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['productvideo_id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['productvideo_id' => $this->getRequest()->getParam('productvideo_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
