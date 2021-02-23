<?php

 
namespace Netbaseteam\Faq\Controller\Adminhtml\Category;
 
use Magento\Backend\App\Action;
 
class Edit extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;
 
    
    protected $resultPageFactory;
 
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
 
   
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Faq::save');
    }
 
   
    protected function _initAction()
    {
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Netbaseteam_Faq::grid')
            ->addBreadcrumb(__('FAQ Category'), __('FAQ Category'))
            ->addBreadcrumb(__('FAQ Category Infomation'), __('FAQ Category Infomation'));
        return $resultPage;
    }
 
    
    public function execute()
    {

        $id = $this->getRequest()->getParam('faq_category_id');
        
        $model = $this->_objectManager->create('Netbaseteam\Faq\Model\Faqcategory');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This staff no longer exists.'));
                
                $resultRedirect = $this->resultRedirectFactory->create();
 
                return $resultRedirect->setPath('*/*/');
            }
        }
 
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
 
        $this->_coreRegistry->register('faq_category', $model);
        
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Grid') : __('New Grid'),
            $id ? __('Edit Grid') : __('New Grid')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Category'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('New FAQ Category'));
 
        return $resultPage;
    }
}