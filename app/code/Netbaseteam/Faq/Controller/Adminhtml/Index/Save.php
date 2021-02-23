<?php

namespace Netbaseteam\Faq\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    protected $_datetime;
    protected $_timezone;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(Action\Context $context, PostDataProcessor $dataProcessor,\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone)
    {
        $this->dataProcessor = $dataProcessor;
        $this->_datetime = $dateTime;
        $this->_timezone = $timezone;
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
            $model = $this->_objectManager->create('Netbaseteam\Faq\Model\Faq');
            $id = $this->getRequest()->getParam('faq_id');

            if ($id) {
                $model->load($id);
            }
            if (!empty($data['question'])) {
               $data['question'] = strip_tags($data['question']);
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

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['faq_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                $imageHelper = $this->_objectManager->get('Netbaseteam\Faq\Helper\Data');

                if (isset($imageData['delete']) && $model->getImage()) {
                    $imageHelper->removeImage($model->getImage());
                    $model->setImage(null);
                }
                
                $imageFile = $imageHelper->uploadImage('image');
                if ($imageFile) {
                    $model->setImage($imageFile);
                }
                
                $model->save();
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['faq_id' => $model->getId(), '_current' => true]);
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
            $this->_redirect('*/*/edit', ['faq_id' => $this->getRequest()->getParam('faq_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
