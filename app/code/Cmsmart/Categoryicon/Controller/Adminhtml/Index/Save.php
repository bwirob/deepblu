<?php

namespace Cmsmart\Categoryicon\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Cmsmart_Categoryicon::save');
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
            $model = $this->_objectManager->create('Cmsmart\Categoryicon\Model\Categoryicon');

            $id = $this->getRequest()->getParam('categoryicon_id');
            if ($id) {
                $model->load($id);
            }
            
            // save image data and remove from data array
            if (isset($data['icon_init'])) {
                $imageData = $data['icon_init'];
                unset($data['icon_init']);
            } else {
                $imageData = array();
            }
			
			if (isset($data['icon_hover'])) {
                $imageData2 = $data['icon_hover'];
                unset($data['icon_hover']);
            } else {
                $imageData2 = array();
            }

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['categoryicon_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                $imageHelper = $this->_objectManager->get('Cmsmart\Categoryicon\Helper\Data');

                if (isset($imageData['delete']) && $model->getIconInit()) {
                    $imageHelper->removeImage($model->getIconInit());
                    $model->setIconInit(null);
                }
				
				if (isset($imageData2['delete']) && $model->getIconHover()) {
                    $imageHelper->removeImage($model->getIconHover());
                    $model->setIconHover(null);
                }
                
                $imageFileInit = $imageHelper->uploadImage('icon_init', $data['category_id']);
                $imageFileHover = $imageHelper->uploadImage('icon_hover', $data['category_id']);
                if ($imageFileInit) {
                    $model->setIconInit($imageFileInit);
                }
				
				if ($imageFileHover) {
                    $model->setIconHover($imageFileHover);
                }
                
                $model->save();
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['categoryicon_id' => $model->getId(), '_current' => true]);
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
            $this->_redirect('*/*/edit', ['categoryicon_id' => $this->getRequest()->getParam('categoryicon_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
