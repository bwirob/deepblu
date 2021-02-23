<?php

namespace Netbaseteam\Blog\Controller\Adminhtml\Comment;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Blog::category_delete');
    }

   
    public function execute()
    {
        $id = $this->getRequest()->getParam('blog_comment_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                $model = $this->_objectManager->create('Netbaseteam\Blog\Model\Comment');
                $model->load($id);
                $title = $model->getTitle();
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
}
