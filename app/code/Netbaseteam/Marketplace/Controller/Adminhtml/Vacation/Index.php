<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Adminhtml\Vacation;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Netbaseteam_Marketplace::marketplace_vacation';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Netbaseteam\Marketplace\Helper\Data
     */
    protected $_helper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Netbaseteam\Marketplace\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_helper->getMessageLicense();
        $helper = $this->_helper;
        $isVacation = $helper->isVacation();

        if ($isVacation == 1) {
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Netbaseteam_Marketplace::marketplace_vacation');
            $resultPage->addBreadcrumb(__('Manage Vacations'), __('Manage Vacations'));
            $resultPage->addBreadcrumb(__('Marketplace'), __('Marketplace'));
            $resultPage->getConfig()->getTitle()->prepend(__('Manage Vacations'));

            return $resultPage;
        } else {
            return $this->_redirect('adminhtml/dashboard/index');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
