<?php

namespace Netbaseteam\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;
    protected $_dataHelper;

    protected $_urlRewriteFactory;


    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
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
        return $this->_authorization->isAllowed('Netbaseteam_Blog::save');
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
            $model = $this->_objectManager->create('Netbaseteam\Blog\Model\Post');

            $id = $this->getRequest()->getParam('post_id');
            if ($id) {
                $model->load($id);
            }
            
            if(!empty($data['store_ids'])){
                if(in_array('0',$data['store_ids'])){
                    $data['store_ids'] = '0';
                }else{
                    $data['store_ids'] = implode(",", $data['store_ids']);    
                }          
            }
            
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            } else {
                $imageData = array();
            }

            if (isset($data['thumbnail'])) {
                $thumbData = $data['thumbnail'];
                unset($data['thumbnail']);
            } else {
                $thumbData = array();
            }

            if (isset($data['author_avatar'])) {
                $authorAvatar = $data['author_avatar'];
                unset($data['author_avatar']);
            } else {
                $authorAvatar = array();
            }


            if (isset($data['feature_image'])) {
                $featureImg = $data['feature_image'];
                unset($data['feature_image']);
            } else {
                $featureImg = array();
            }

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['post_id' => $model->getPostId(), '_current' => true]);
                return;
            }

            try {
                $imageHelper = $this->_objectManager->get('Netbaseteam\Blog\Helper\Data');

                if (isset($imageData['delete']) && $model->getImage()) {
                    $imageHelper->removeImage($model->getImage(),'image');
                    $model->setImage(null);
                }
                
                $imageFile = $imageHelper->uploadImage('image');
                if ($imageFile) {
                    $model->setImage($imageFile);
                }

                if (isset($thumbData['delete']) && $model->getThumbnail()) {
                    $imageHelper->removeImage($model->getThumbnail(),'thumbnail');
                    $model->setThumbnail(null);
                }
                
                $thumbFile = $imageHelper->uploadImage('thumbnail');
                if ($thumbFile) {
                    $model->setThumbnail($thumbFile);
                }

                if (isset($authorAvatar['delete']) && $model->getAuthorAvatar()) {
                    $imageHelper->removeImage($model->getAuthorAvatar(),'author_avatar');
                    $model->setAuthorAvatar(null);
                }
                $authorAvatarFile = $imageHelper->uploadImage('author_avatar');

                
                if ($authorAvatarFile) {
                    $model->setAuthorAvatar($authorAvatarFile);
                }

                if (isset($featureImg['delete']) && $model->getFeatureImage()) {
                    $imageHelper->removeImage($model->getFeatureImage(),'feature_image');
                    $model->setFeatureImage(null);
                }

                $featureImgFile = $imageHelper->uploadImage('feature_image');
                if ($featureImgFile) {
                    $model->setFeatureImage($featureImgFile);
                }
                $model->save();

                $postId = $model->getId();
                if(empty($data['url_rewrite_id'])){
                    $urlRewriteId = $this->createUrlRewrites($data['identifier'],$postId);
                    $model->load($postId)->setUrlRewriteId($urlRewriteId);
                    $model->save();
                }else{
                    $this->updateUrlRewrite($data['identifier'],$data['url_rewrite_id']);
                    // $this->_urlRewriteFactory->create()->load($data['url_rewrite_id'])->delete();
                    // $urlRewriteId = $this->createUrlRewrites($data['identifier'],$postId);
                    // $model->load($postId)->setUrlRewriteId($urlRewriteId);
                    // $model->save();

                }

                
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['post_id' => $model->getPostId(), '_current' => true]);
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
            $this->_redirect('*/*/edit', ['post_id' => $this->getRequest()->getParam('post_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }

    public function createUrlRewrites($identifier,$postId){
        try {
                $baseUrl = $this->_dataHelper->getPreBlogUrl();
                $urlRewriteIds = [];

                $storeIds = $this->getAllStoreIds();

                foreach ($storeIds as $storeId) {
                     $requestPath = 'blog/'.$identifier;
                    $targetPath = 'blog/post/index/post_id/'.$postId;
                    $data = array(
                            'url_rewrite_id'=>null,
                            'entity_type'=>'blog-post',
                            'entity_id' =>$postId,
                            'request_path'=>$requestPath,
                            'target_path'=>$targetPath,
                            'store_id'=>$storeId
                        );

                    $urlRewriteModel = $this->_urlRewriteFactory->create();
                    $urlRewriteModel->addData($data);
                    $urlRewriteModel->save();
                    $urlRewriteId = $urlRewriteModel->getId();
                    $urlRewriteIds[] =  $urlRewriteId;
                }
               
                $ids = implode("&",$urlRewriteIds);
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                return $ids;

            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                //$model = $this->_objectManager->create('Netbaseteam\Blog\Model\Post');
                //$model->load($postId)->delete();
                $this->messageManager->addException($e, __('This identifier already exit, Please set other Identifier for this post'));
            }
        
    }

    public function updateUrlRewrite($identifier,$urlRewriteIds){
        try {
                $baseUrl = $this->_dataHelper->getPreBlogUrl();
                $requestPath = 'blog/'.$identifier;
                $urlRewriteIds = explode("&", $urlRewriteIds);

                foreach ($urlRewriteIds as $urlRewriteId) {
                    $data = array(
                        'url_rewrite_id'=>$urlRewriteId,
                        'request_path'=>$requestPath
                    );
                    $urlRewriteModel = $this->_urlRewriteFactory->create();
                    $urlRewriteModel->addData($data);
                    $urlRewriteModel->save();
                }
               
                

               
                $this->messageManager->addSuccess(__('The Data has been saved.'));

            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('This identifier already exit, Please set other Identifier for this category'));
            }
        
    }

     public function getAllStoreIds(){
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $stores = $storeManager->getStores($withDefault = false);
        $storeIds = [];
        foreach($stores as $store) {
            $storeIds[] = $store->getStoreId();
        }

        return $storeIds;

    }   
}
