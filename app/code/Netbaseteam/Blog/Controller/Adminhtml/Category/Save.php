<?php

namespace Netbaseteam\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    protected $_urlRewriteFactory;

    protected $_dataHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(
        Action\Context $context, 
        PostDataProcessor $dataProcessor,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
        \Netbaseteam\Blog\Helper\Data $dataHelper,
        \Magento\Framework\Webapi\Rest\Request $request
    ){
        $this->dataProcessor = $dataProcessor;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_dataHelper = $dataHelper;
        $this->_request = $request;
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

            if (!isset($data['post_ids'])) {
                if ($data['post_ids_callback']==''){
                    $this->messageManager->addError(__('Please select Posts for this Category in Select Post tab'));
                    $this->_getSession()->setFormData($data);
                    $this->_redirect('*/*/edit', ['blog_category_id' => $this->getRequest()->getParam('blog_category_id')]);
                    return;
                }  
            }else{
                if ($data['post_ids']==''){
                    $this->messageManager->addError(__('Please select Posts for this Category in Select Post tab'));
                    $this->_getSession()->setFormData($data);
                    $this->_redirect('*/*/edit', ['blog_category_id' => $this->getRequest()->getParam('blog_category_id')]);
                    return;
                }  
            }
            
            $model = $this->_objectManager->create('Netbaseteam\Blog\Model\Category');
            $id = $this->getRequest()->getParam('blog_category_id');
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
            
            if (isset($data['category_image'])) {
                $imageData = $data['category_image'];
                unset($data['category_image']);
            } else {
                $imageData = array();
            }

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['blog_category_id' => $model->getBlogCategoryId(), '_current' => true]);
                return;
            }

            try {
                
                $imageHelper = $this->_objectManager->get('Netbaseteam\Blog\Helper\Data');

                if (isset($imageData['delete']) && $model->getCategoryImage()) {
                    $imageHelper->removeImage($model->getCategoryImage(),'category_image');
                    $model->setImage(null);
                }
                
                $imageFile = $imageHelper->uploadImage('category_image');
                if ($imageFile) {
                    $model->setCategoryImage($imageFile);
                }

                $model->save();

                $categoryId = $model->getId();
                if(empty($data['url_rewrite_id'])){
                    $urlRewriteId = $this->createUrlRewrite($data['identifier'],$categoryId);
                    $model->load($categoryId)->setUrlRewriteId($urlRewriteId);
                    $model->save();
                }else{
                    $this->updateUrlRewrite($data['identifier'],$data['url_rewrite_id']);
                }
                
                
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['blog_category_id' => $model->getBlogCategoryId(), '_current' => true]);
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
            $this->_redirect('*/*/edit', ['blog_category_id' => $this->getRequest()->getParam('blog_category_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }


    public function createUrlRewrite($identifier,$categoryId){
        try {
                $baseUrl = $this->_dataHelper->getPreBlogUrl();
                $storeIds = $this->getAllStoreIds();
                foreach ($storeIds as $storeId) {
                    $requestPath = 'blog/'.$identifier;
                    $targetPath = 'blog/category/index/blog_category_id/'.$categoryId;
                    $data = array(
                            'url_rewrite_id'=>null,
                            'entity_type'=>'blog-category',
                            'entity_id' =>$categoryId,
                            'request_path'=>$requestPath,
                            'target_path'=>$targetPath,
                            'store_id'=>$storeId
                        );

                    $urlRewriteModel = $this->_urlRewriteFactory->create();
                    $urlRewriteModel->addData($data);
                    $urlRewriteModel->save();
                }
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $urlRewriteId = $urlRewriteModel->getId();
                return $urlRewriteId;

            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $model = $this->_objectManager->create('Netbaseteam\Blog\Model\Category');
                $model->load($categoryId)->delete();
                $this->messageManager->addException($e, __('This identifier already exit, Please set other Identifier for this category'));
            }
        
    }

    public function updateUrlRewrite($identifier,$urlRewriteId){
        try {
            $urlRewriteModel = $this->_urlRewriteFactory->create();
            $baseUrl = $this->_dataHelper->getPreBlogUrl();
            $allStoreIds = $this->getAllStoreIds();

            if ($categoryId = $this->getCategoryId()) {
                $collection = $urlRewriteModel->getCollection()->addFieldToFilter('request_path', 'blog/' . $identifier);
                $storeIds = [];
                foreach ($collection as $data) {
                    $storeIds[] = $data['store_id'];
                }
                $diffStoreIds = array_diff($allStoreIds, $storeIds);                
                if (!empty($diffStoreIds)) {
                    foreach ($diffStoreIds as $id) {
                        if (in_array($id, $this->_request->getParam('store_ids'))) {
                            $requestPath = 'blog/'.$identifier;
                            $targetPath = 'blog/category/index/blog_category_id/'.$categoryId;
                            $data = array(
                                    'url_rewrite_id'=>null,
                                    'entity_type'=>'blog-category',
                                    'entity_id' =>$categoryId,
                                    'request_path'=>$requestPath,
                                    'target_path'=>$targetPath,
                                    'store_id'=>$id
                                );

                            $urlRewriteModel = $this->_urlRewriteFactory->create();
                            $urlRewriteModel->addData($data);
                            $urlRewriteModel->save();
                        }
                    }
                }
            }

            $requestPath = 'blog/' . $identifier;
            $urlRewriteIds = explode("&", $urlRewriteId);

            foreach ($urlRewriteIds as $id) {
                $data = array(
                    'url_rewrite_id' => $id,
                    'request_path' => $requestPath
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

    protected function getCategoryId()
    {
        $model = $this->_objectManager->create('Netbaseteam\Blog\Model\Category');
        $id = $this->getRequest()->getParam('blog_category_id');        
        if ($id) {
            $model->load($id);
            return $model->getId();
        }
        return false;
    }

    protected function getAllStoreIds()
    {
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $stores = $storeManager->getStores($withDefault = false);
        $storeIds = [];
        foreach ($stores as $store) {
            $storeIds[] = $store->getStoreId();
        }

        return $storeIds;

    }


}
