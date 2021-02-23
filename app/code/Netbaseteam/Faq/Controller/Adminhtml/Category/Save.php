<?php

namespace Netbaseteam\Faq\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    protected $_dateTime;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(
        Action\Context $context, 
        PostDataProcessor $dataProcessor,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    )
    {
        $this->dataProcessor = $dataProcessor;
        $this->_dateTime = $dateTime;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Faq::save');
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
            $model = $this->_objectManager->create('Netbaseteam\Faq\Model\Faqcategory');
            $id = $this->getRequest()->getParam('faq_category_id');        
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
            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['faq_category_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {

                $imageHelper = $this->_objectManager->get('Netbaseteam\Faq\Helper\Data');

                if (isset($imageData['delete']) && $model->getIcon()) {
                    $imageHelper->removeImage($model->getIcon());
                    $model->setIcon(null);
                }
                
                $imageFile = $imageHelper->uploadImage('icon');

                if ($imageFile) {
                    $model->setIcon($imageFile);
                }

                $model->save();
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['faq_category_id' => $model->getId(), '_current' => true]);
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
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['faq_category_id' => $this->getRequest()->getParam('faq_category_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
