<?php

namespace Netbaseteam\Locator\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    protected $_urlRewriteFactory;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(
        Action\Context $context, 
        PostDataProcessor $dataProcessor,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory
    ){
        $this->dataProcessor = $dataProcessor;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Locator::save');
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
            $model = $this->_objectManager->create('Netbaseteam\Locator\Model\Locator');

            $id = $this->getRequest()->getParam('localtor_id');
            if ($id) {
                $model->load($id);
            }
            
            
            if (isset($data['store_image'])) {
                $imageData = $data['store_image'];
                unset($data['store_image']);
            } else {
                $imageData = array();
            }

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['localtor_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                $imageHelper = $this->_objectManager->get('Netbaseteam\Locator\Helper\Data');

                if (isset($imageData['delete']) && $model->getStoreImage()) {
                    $imageHelper->removeImage($model->getStoreImage());
                    $model->setStoreImage(null);
                }
                
                $imageFile = $imageHelper->uploadImage('store_image');
                if ($imageFile) {
                    $model->setStoreImage($imageFile);
                }
                
                $model->save();

                $localtorId = $model->getId();
                if(empty($data['url_rewrite_id'])){
                    $urlRewriteId = $this->createUrlRewrite($data['identifier'],$localtorId);
                    $model->load($localtorId)->setUrlRewriteId($urlRewriteId);
                    $model->save();
                }else{
                    $this->updateUrlRewrite($data['identifier'],$data['url_rewrite_id']);
                }

                
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['localtor_id' => $model->getId(), '_current' => true]);
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
            $this->_redirect('*/*/edit', ['localtor_id' => $this->getRequest()->getParam('localtor_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }

    public function createUrlRewrite($identifier,$localtorId){
        try {
               
                $requestPath = 'locator/'.$identifier;
                $targetPath = 'locator/store/index/id/'.$localtorId;
                $data = array(
                        'url_rewrite_id'=>null,
                        'entity_type'=>'localtor-view',
                        'entity_id' =>$localtorId,
                        'request_path'=>$requestPath,
                        'target_path'=>$targetPath,
                        'store_id'=>'1'
                    );

                $urlRewriteModel = $this->_urlRewriteFactory->create();
                $urlRewriteModel->addData($data);
                $urlRewriteModel->save();
                $urlRewriteId = $urlRewriteModel->getId();
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                return $urlRewriteId;

            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $model = $this->_objectManager->create('Netbaseteam\Locator\Model\Locator');
                $model->load($localtorId)->delete();
                $this->messageManager->addException($e, __('This identifier already exit, Please set other Identifier for this Store Location'));
            }
        
    }

    public function updateUrlRewrite($identifier,$urlRewriteId){
        try {
               
                $requestPath = 'locator/'.$identifier;
               
                $data = array(
                        'url_rewrite_id'=>$urlRewriteId,
                        'request_path'=>$requestPath,
                        'store_id'=>'1'
                    );

                $urlRewriteModel = $this->_urlRewriteFactory->create();
                $urlRewriteModel->addData($data);
                $urlRewriteModel->save();
                $this->messageManager->addSuccess(__('The Data has been saved.'));

            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('This identifier already exit, Please set other Identifier for this category'));
            }
    }   
}
