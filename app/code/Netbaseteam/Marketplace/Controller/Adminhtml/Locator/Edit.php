<?php

namespace Netbaseteam\Marketplace\Controller\Adminhtml\Locator;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Netbaseteam_Marketplace::marketplace_locator';
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
     * @var \Netbaseteam\Marketplace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        $this->_messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
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
            'Netbaseteam_Marketplace::locator'
        )->addBreadcrumb(
            __('Locator'),
            __('Locator')
        )->addBreadcrumb(
            __('Manage Seller Locator'),
            __('Manage Seller Locator')
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
        if ($this->_helper->releaseLimit()) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Location');

            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    $this->messageManager->addError(__('This locator no longer exists.'));
                    /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            $this->_coreRegistry->register('current_locator', $model);

            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->_initAction();
            $resultPage->addBreadcrumb(
                $id ? __('Edit Locator') : __('New Locator'),
                $id ? __('Edit Locator') : __('New Locator')
            );
            $resultPage->getConfig()->getTitle()->prepend(__('Locator'));
            $resultPage->getConfig()->getTitle()
                ->prepend($model->getId() ? $model->getStoreName() : __('New Locator'));
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
}
