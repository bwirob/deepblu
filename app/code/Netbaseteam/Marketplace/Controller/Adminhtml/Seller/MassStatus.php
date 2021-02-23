<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Adminhtml\Seller;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Netbaseteam\Marketplace\Controller\Adminhtml\AbstractMassAction;
use Netbaseteam\Marketplace\Model\ResourceModel\Seller\CollectionFactory;
use Netbaseteam\Marketplace\Model\SellerFactory;

/**
 * Class MassDelete
 */
class MassStatus extends AbstractMassAction
{
    const ADMIN_RESOURCE = 'Netbaseteam_Marketplace::marketplace_seller';

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
        SellerFactory $manageFactory,
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
                $this->sendMail($model);
                $itemsSelected++;
            }

            if ($itemsSelected) {
                $this->messageManager->addSuccess(__('A total of %1 seller(s) were updated.', $itemsSelected));
            } else {
                $this->messageManager->addErrorMessage('Something went wrong while update seller');
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

    private function sendMail($data)
    {
        $helper = $this->_marketplaceHelperData;
        $sellerName = '';
        $sellerEmail = '';
        $sellerId = '';

        if ($data) {
            $sellerId = $data['seller_id'];
        }

        if ($sellerId) {
            $customer = $this->_objectManager->get(
                'Magento\Customer\Model\Customer'
            )->load($sellerId);

            $sellerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $sellerEmail = $customer->getEmail();
        }


        $emailTempVariables = [];
        $adminStoremail = $helper->getAdminEmailId();
        $adminEmail = $adminStoremail ?
            $adminStoremail : $helper->getDefaultTransEmailId();
        $adminUsername = 'Admin';

        $emailTempVariables['admin'] = $sellerName;
        $emailTempVariables['templateSubject'] = 'DemoShop';

        if ($data['status'] == 1) {
            $emailTempVariables['seller_status'] = __("approved");

            $profileUrl = $this->urlBuilder->getUrl(
                'marketplace/seller/editprofile/'
            );

            $emailTempVariables['profile_url'] = __("Please access website $profileUrl to publish your item");
            $emailTempVariables['email-footer'] = __("Hope you have great experience on our website!");
        } else {
            $emailTempVariables['seller_status'] = __("disapproved");
        }

        $senderInfo = [
            'name' => $adminUsername,
            'email' => $adminEmail,
        ];
        $receiverInfo = [
            'name' => $sellerName,
            'email' => $sellerEmail,
        ];

        $helper->sendSellerMail(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}