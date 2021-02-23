<?php

namespace Netbaseteam\Locator\Controller\Adminhtml\Schedule;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Locator::locator_delete');
    }

    /**
     * Delete action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('schedule_id');
		/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                $model = $this->_objectManager->create('Netbaseteam\Locator\Model\Schedule');
                $model->load($id);
                $title = $model->getScheduleName();
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
