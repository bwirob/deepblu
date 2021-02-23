<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Adminhtml\Locator;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Netbaseteam\Marketplace\Controller\Adminhtml\AbstractMassAction;
use Netbaseteam\Marketplace\Model\ResourceModel\Location\CollectionFactory;
use Netbaseteam\Marketplace\Model\LocationFactory;

/**
 * Class MassDelete
 */
class MassStatus extends AbstractMassAction
{
    const ADMIN_RESOURCE = 'Netbaseteam_Marketplace::marketplace_locator';

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
        LocationFactory $manageFactory,
        \Netbaseteam\Marketplace\Helper\Data $marketplaceHelperData,
        \Magento\Framework\Url $urlBuilder,
        \Netbaseteam\Marketplace\Helper\Data $helper
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->model = $manageFactory;
        $this->_marketplaceHelperData  = $marketplaceHelperData;
        $this->urlBuilder = $urlBuilder;
        $this->_helper = $helper;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction($collection)
    {
        if ($this->_helper->releaseLimit()) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $status = (int) $this->getRequest()->getParam('status');

            $itemsSelected = 0;
            foreach ($collection->getAllIds() as $itemId) {
                $model = $this->model->create()->load($itemId);
                $model->setStatus($status);
                $model->save();
                $itemsSelected++;
            }

            if ($itemsSelected) {
                $this->messageManager->addSuccess(__('A total of %1 locator(s) were updated.', $itemsSelected));
            } else {
                $this->messageManager->addErrorMessage('Something went wrong while update locator');
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