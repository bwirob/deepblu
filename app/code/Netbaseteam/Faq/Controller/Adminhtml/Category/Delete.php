<?php

namespace Netbaseteam\Faq\Controller\Adminhtml\Category;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Faq::faq_delete');
    }

    /**
     * Delete action
     *
     * @return void
     */
    public function execute()
    {
        
        $id = $this->getRequest()->getParam('faq_category_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                
                $model = $this->_objectManager->create('Netbaseteam\Faq\Model\Faqcategory');
                $model->load($id);
                $title = $model->getName();
                $model->delete();
                $this->messageManager->addSuccess(__('The data has been deleted.'));
                return $resultRedirect->setPath('*/category');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/edit', ['page_id' => $id]);
            }
        }    
        $this->messageManager->addError(__('We can\'t find a data to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
