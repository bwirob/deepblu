<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Shopbybrand\Controller\Adminhtml\MassAction;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Netbaseteam\Shopbybrand\Controller\Adminhtml\AbstractMassAction;
/**
 * Class MassDelete
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Netbaseteam\Shopbybrand\Model\ResourceModel\Shopbybrand\CollectionFactory $collectionFactory,
        \Netbaseteam\Shopbybrand\Model\ShopbybrandFactory $ShopbybrandFactory
        
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->model = $ShopbybrandFactory;
        
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction($collection)
    {
        

        $itemsDeleted = 0;
        $productDeleted = 0;
        foreach ($collection as $item) {
            $this->model->create()->load($item->getId())->delete();
            $itemsDeleted++;

            $this->model->create()->load($item->getProductId())->delete();
            $productDeleted++;
        }

        if ($itemsDeleted) {
            $this->messageManager->addSuccess(__('A total of %1 product(s) were deleted.', $itemsDeleted));
        } else {
            $this->messageManager->addErrorMessage('Something went wrong while delete product');
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    
}