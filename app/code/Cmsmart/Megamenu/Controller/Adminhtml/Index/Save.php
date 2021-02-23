<?php

namespace Cmsmart\Megamenu\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Cmsmart_Megamenu::save');
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
			/* \zend_debug::dump($data);die; */
            $model = $this->_objectManager->create('Cmsmart\Megamenu\Model\Megamenu');

            $id = $this->getRequest()->getParam('megamenu_id');
            if ($id) {
                $model->load($id);
            }
            
			/* process sku */
			/* $objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
			$helper = $objectManagerr->get('\Cmsmart\Megamenu\Helper\Data');
			if($data["top_block_left"]){
				$mydt = array();
				$mydt = $helper->getTitleSkuFromPattern($data["top_block_left"]);
				$data["top_left_sku_title"] = $mydt["tilte"];
				$data["top_left_block_sku"] = $mydt["sku"];
			} else {
				$data["top_block_left"] = "";
			}
			
			if($data["top_block_right"]){
				$mydt = array();
				$mydt = $helper->getTitleSkuFromPattern($data["top_block_right"]);
				$data["top_right_sku_title"] = $mydt["tilte"];
				$data["top_right_block_sku"] = $mydt["sku"];
			} else {
				$data["top_block_right"] = "";
			} */
			/* end process sku */
			
            // save image data and remove from data array
            if (isset($data['left_cat_icon'])) {
                $imageData = $data['left_cat_icon'];
                unset($data['left_cat_icon']);
            } else {
                $imageData = array();
            }

            $model->addData($data);

            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['megamenu_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                $imageHelper = $this->_objectManager->get('Cmsmart\Megamenu\Helper\Data');

                if (isset($imageData['delete']) && $model->getLeftCatIcon()) {
                    $imageHelper->removeImage($model->getLeftCatIcon());
                    $model->setLeftCatIcon(null);
                }
                
                $imageFile = $imageHelper->uploadImage('left_cat_icon');
                if ($imageFile) {
                    $model->setLeftCatIcon($imageFile);
                }
                
                $model->save();
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['megamenu_id' => $model->getId(), '_current' => true]);
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
            $this->_redirect('*/*/edit', ['megamenu_id' => $this->getRequest()->getParam('megamenu_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
