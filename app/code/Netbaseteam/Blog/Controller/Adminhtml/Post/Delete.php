<?php

namespace Netbaseteam\Blog\Controller\Adminhtml\Post;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Blog::post_delete');
    }

   
    public function execute()
    {

        $id = $this->getRequest()->getParam('post_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                $model = $this->_objectManager->create('Netbaseteam\Blog\Model\Post');
                $model->load($id);
                $title = $model->getTitle();
                $urlRewriteId = $model->getUrlRewriteId();
                $this->deleteUrlRewrite($urlRewriteId);
                $model->delete();
                $model->delete();
                $this->messageManager->addSuccess(__('The data has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['page_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a data to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    public function deleteUrlRewrite($urlRewriteId){
        $urlRewriteModel = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite');
        $urlRewriteModel->load($urlRewriteId);
        $urlRewriteModel->delete();

    }
}
