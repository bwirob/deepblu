<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Netbaseteam\Marketplace\Controller\Adminhtml\AbstractMassAction;
use Netbaseteam\Marketplace\Model\ResourceModel\Order\CollectionFactory;
use Netbaseteam\Marketplace\Model\OrderFactory;

/**
 * Class MassDelete
 */
class MassDelete extends AbstractMassAction
{
    const ADMIN_RESOURCE = 'Netbaseteam_Marketplace::marketplace_order';

    /**
     * @var \Netbaseteam\Marketplace\Helper\Data
     */
    protected $_helper;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderFactory $manageFactory,
        \Netbaseteam\Marketplace\Helper\Data $helper
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->model = $manageFactory;
        $this->_helper = $helper;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction($collection)
    {
        if ($this->_helper->releaseLimit()) {
            $itemsDeleted = 0;
            foreach ($collection as $item) {
                $this->model->create()->load($item->getId())->delete();
                $itemsDeleted++;
            }

            if ($itemsDeleted) {
                $this->messageManager->addSuccess(__('A total of %1 order(s) were deleted.', $itemsDeleted));
            } else {
                $this->messageManager->addErrorMessage('Something went wrong while delete order');
            }
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->getComponentRefererUrl());

            return $resultRedirect;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
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