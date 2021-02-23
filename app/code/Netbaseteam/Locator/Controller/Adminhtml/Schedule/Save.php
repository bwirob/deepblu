<?php

namespace Netbaseteam\Locator\Controller\Adminhtml\Schedule;

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
    public function __construct(Action\Context $context, \Netbaseteam\Locator\Controller\Adminhtml\Index\PostDataProcessor $dataProcessor,\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone)
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
        return $this->_authorization->isAllowed('Netbaseteam_Locator::save');
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
            $model = $this->_objectManager->create('Netbaseteam\Locator\Model\Schedule');
            $id = $this->getRequest()->getParam('schedule_id');
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            } else {
                $imageData = array();
            }
            $scheContents = array();
            foreach ($data as $key => $dataItem){
                if($key != 'status'&& $key != 'schedule_name'&& $key != 'form_key' && $key != 'schedule_id'){
                    $scheContents[$key] = $dataItem;
                    unset($data[$key]);

                }
            }


            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['schedule_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                $imageHelper = $this->_objectManager->get('Netbaseteam\Locator\Helper\Data');

                if (isset($imageData['delete']) && $model->getImage()) {
                    $imageHelper->removeImage($model->getImage());
                    $model->setImage(null);
                }
                
                $imageFile = $imageHelper->uploadImage('image');
                if ($imageFile) {
                    $model->setImage($imageFile);
                }
                
                $model->save();
                $scheContents['schedule_id'] = $model->getId();
                
                if(!empty($data['schedule_id'])){
                    $this->updateSchContent($scheContents);
                }else{
                    $this->createSchContent($scheContents);
                }

                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['schedule_id' => $model->getId(), '_current' => true]);
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
            $this->_redirect('*/*/edit', ['schedule_id' => $this->getRequest()->getParam('schedule_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }

    public function createSchContent($contentInfo){
        $dateModel = $this->_objectManager->create('Netbaseteam\Locator\Model\Workdate');
        for ($i=1;$i<8; $i++) { 
            $dateW = 'd'.$i;
            $data =  array();
            $keyOpen = $dateW.'_open';
            $keyClose = $dateW.'_close';
            $keyStatus = $dateW.'_status';
            $data = [
                'work_date_id' => null,
                'schedule_id' =>$contentInfo['schedule_id'],
                'status' => $contentInfo[$keyStatus],
                'open_time'=> implode(',',$contentInfo[$keyOpen]),
                'close_time'=> implode(',',$contentInfo[$keyClose]),
                'date_w' => $dateW
            ];
            $dateModel->addData($data);
            $dateModel->save();
        }

    }

    public function updateSchContent($contentInfo){
        for ($i=1;$i<8; $i++) { 
            $dateModel = $this->_objectManager->create('Netbaseteam\Locator\Model\Workdate');
            $workdateIds = explode(',', $contentInfo['work_date_ids']);
            $dateW = 'd'.$i;
            $data =  array();
            $keyOpen = $dateW.'_open';
            $keyClose = $dateW.'_close';
            $keyStatus = $dateW.'_status';
            
            $data = [
                'work_date_id' =>  $workdateIds[$i-1],
                'schedule_id' =>$contentInfo['schedule_id'],
                'status' => $contentInfo[$keyStatus],
                'open_time'=> implode(',',$contentInfo[$keyOpen]),
                'close_time'=> implode(',',$contentInfo[$keyClose]),
                'date_w' => $dateW
            ];
            $dateModel->addData($data);
            $dateModel->save();
        }
        
    }
}
