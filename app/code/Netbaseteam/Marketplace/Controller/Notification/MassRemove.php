<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Notification;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action;
use Netbaseteam\Marketplace\Model\ResourceModel\Notification\CollectionFactory;

class MassRemove extends \Magento\Framework\App\Action\Action
{

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Action\Context $context
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        CollectionFactory $collectionFactory,
        \Netbaseteam\Marketplace\Model\NotificationFactory $notificationFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Ui\Component\MassAction\Filter $filter
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->notificationFactory = $notificationFactory;
        $this->formkey = $formKey;
        $this->request = $request;
        $this->request->setParam('form_key', $this->formkey->getFormKey());
        $this->customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        if ($isPartner) {

            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $notificationDeleted = 0;
            /** @var \Magento\Catalog\Model\Product $notification */
            foreach ($collection->getItems() as $item) {
                $this->notificationFactory->create()->load($item->getId())->delete();
                $notificationDeleted++;
            }

            if ($notificationDeleted) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been removed.', $notificationDeleted)
                );
            }
            return $this->resultRedirectFactory->create()->setPath(
                '*/*/',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/registry',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
