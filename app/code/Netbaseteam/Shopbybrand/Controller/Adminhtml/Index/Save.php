<?php

namespace Netbaseteam\Shopbybrand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    protected $dataProcessor;
    protected $_dataHelper;
    protected $_urlRewriteFactory;


    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        \Netbaseteam\Shopbybrand\Helper\Data $dataHelper,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory
    )
    {
        $this->dataProcessor = $dataProcessor;
        $this->_dataHelper = $dataHelper;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Shopbybrand::save');
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
            $model = $this->_objectManager->create('Netbaseteam\Shopbybrand\Model\Shopbybrand');

            $id = $this->getRequest()->getParam('brand_id');
            if ($id) {
                $model->load($id);
            }
            
            // save logo data and remove from data array
            if (isset($data['logo'])) {
                $imageData = $data['logo'];
                unset($data['logo']);
            } else {
                $imageData = array();
            }

            if (isset($data['banner'])) {
                $thumbData = $data['banner'];
                unset($data['banner']);
            } else {
                $thumbData = array();
            }

            if(!empty($data['store_ids'])){
                if(in_array('0',$data['store_ids'])){
                    $data['store_ids'] = '0';
                }else{
                    $data['store_ids'] = implode(",", $data['store_ids']);    
                }          
            }
           
            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['brand_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                $imageHelper = $this->_objectManager->get('Netbaseteam\Shopbybrand\Helper\Data');

                if (isset($imageData['delete']) && $model->getLogo()) {
                    $imageHelper->removeImage($model->getLogo());
                    $model->setLogo(null);
                }
                
                $imageFile = $imageHelper->uploadImage('logo');
                if ($imageFile) {
                    $model->setLogo($imageFile);
                }


               

                if (isset($thumbData['delete']) && $model->getBanner()) {
                    $imageHelper->removeImage($model->getBanner());
                    $model->setBanner(null);
                  
                }
                
                $thumbFile = $imageHelper->uploadImage('banner');
                if ($thumbFile) {
                    $model->setBanner($thumbFile);
                }
                
                $model->save();
                $postId = $model->getId();
                if(empty($data['url_rewrite_id'])){
                    $urlRewriteId = $this->createUrlRewrite($data['urlkey'],$postId);
                    $model->load($postId)->setUrlRewriteId($urlRewriteId);
                    $model->save();
                }else{
                    $this->updateUrlRewrite($data['urlkey'],$data['url_rewrite_id']);
                }
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['brand_id' => $model->getId(), '_current' => true]);
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
            $this->_redirect('*/*/edit', ['brand_id' => $this->getRequest()->getParam('brand_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
    public function createUrlRewrite($identifier,$postId){
        try {
                $baseUrl = $this->_dataHelper->getPreBlogUrl();
               
                $requestPath = 'shopbybrand/'.$identifier;
                $targetPath = 'shopbybrand/index/view/id/'.$postId.'/';
                $data = array(
                        'url_rewrite_id'=>null,
                        'entity_type'=>'shopbybrand-view',
                        'entity_id' =>$postId,
                        'request_path'=>$requestPath,
                        'target_path'=>$targetPath,
                        'store_id'=>'1'
                    );

                $urlRewriteModel = $this->_urlRewriteFactory->create();
                $urlRewriteModel->addData($data);
                $urlRewriteModel->save();
                $urlRewriteId = $urlRewriteModel->getId();
                return $urlRewriteId;

            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('This identifier already exit, Please set other Identifier for this post'));
            }
        
    }

    public function updateUrlRewrite($identifier,$urlRewriteId){
        try {
                $baseUrl = $this->_dataHelper->getPreBlogUrl();
               
                $requestPath = 'shopbybrand/'.$identifier;
               
                $data = array(
                        'url_rewrite_id'=>$urlRewriteId,
                        'request_path'=>$requestPath,
                        'store_id'=>'1'
                    );

                $urlRewriteModel = $this->_urlRewriteFactory->create();
                $urlRewriteModel->addData($data);
                $urlRewriteModel->save();
                

            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('This identifier already exit, Please set other Identifier for this category'));
            }
        
    }
}
