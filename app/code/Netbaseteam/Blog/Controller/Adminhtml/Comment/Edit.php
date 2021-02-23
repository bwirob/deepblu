<?php

namespace Netbaseteam\Blog\Controller\Adminhtml\Comment;

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
        return $this->_authorization->isAllowed('Netbaseteam_Blog::save');
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
            'Netbaseteam_Blog::blog_manage'
        )->addBreadcrumb(
            __('Comment'),
            __('Comment')
        )->addBreadcrumb(
            __('Manage Comment'),
            __('Manage Comment')
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
        $id = $this->getRequest()->getParam('blog_comment_id');
        $model = $this->_objectManager->create('Netbaseteam\Blog\Model\Comment');
        if ($id) {
            $model->load($id);
            if (!$model->getBlogCommentId()) {
                $this->messageManager->addError(__('This Comment no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('comment', $model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Comment') : __('New Comment'),
            $id ? __('Edit Comment') : __('New Comment')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Comment'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getBlogCommentId() ? __('Edit Comment') : __('New Comment'));
        return $resultPage;
    }
}
