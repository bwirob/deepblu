<?php

namespace Netbaseteam\Faq\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
	
	/**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry)
    {
        $this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
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
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
    
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Netbaseteam_Faq::faq_manage'
        )->addBreadcrumb(
            __('FAQ'),
            __('FAQ')
        )->addBreadcrumb(
            __('Manage FAQ'),
            __('Manage FAQ')
        );
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return void
     */
    public function execute()
    {
        
        $id = $this->getRequest()->getParam('faq_id');
        $model = $this->_objectManager->create('Netbaseteam\Faq\Model\Faq');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This faq no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('faq', $model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit FAQ') : __('New FAQ'),
            $id ? __('Edit FAQ') : __('New FAQ')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? strip_tags($model->getQuestion()) : __('New FAQ '));
        return $resultPage;
    }
}
