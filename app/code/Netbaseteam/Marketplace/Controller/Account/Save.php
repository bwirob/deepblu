<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Account;

use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var Netbaseteam\Marketplace\Helper\Data
     */
    protected $_marketplaceHelperData;

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Netbaseteam\Marketplace\Helper\Data $marketplaceHelperData
    )
    {
        $this->_registry = $registry;
        $this->customerSession = $customerSession;
        $this->_dateFactory = $dateFactory;
        $this->_marketplaceHelperData = $marketplaceHelperData;
        parent::__construct($context);
    }

    /**
     * Display marketplace form registry
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $sellerId = $this->customerSession->getId();
            $model = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Seller');
            $model->setSellerId($sellerId);
            $model->setStatus(0);
            $model->setCreatedAt($this->_dateFactory->create()->gmtDate());

            $commissionAmount = 0;
            $commissionOption = 0;
            $commissionType = 0;

            if ($this->_marketplaceHelperData->getCommissionAmount()) {
                $commissionAmount = $this->_marketplaceHelperData->getCommissionAmount();
            }
            if ($this->_marketplaceHelperData->getCommissionOption()) {
                $commissionOption = $this->_marketplaceHelperData->getCommissionOption();
            }
            if ($this->_marketplaceHelperData->getCommissionType()) {
                $commissionType = $this->_marketplaceHelperData->getCommissionType();
            }

            $model->setCommissionAmount($commissionAmount);
            $model->setCommissionType($commissionType);
            $model->setFixedOrPercentage($commissionOption);

            try {
                $model->save();
                $this->sendAccountMail($data, $sellerId);

                $this->messageManager->addSuccess(__('A message has been sent to admin to request approval.'));
                return $resultRedirect->setPath('*/*/registry');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the block.'));
            }
        } else {
            return $resultRedirect->setPath('*/*/registry');
        }
    }

    /**
     * @param array $data
     * @param string $sellerId
     */
    private function sendAccountMail($data, $sellerId)
    {
        $helper = $this->_marketplaceHelperData;
        $sellerName = '';
        $sellerEmail = '';
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

        $emailTempVariables['admin'] = $adminUsername;
        $emailTempVariables['seller_email'] = $sellerEmail;
        $emailTempVariables['seller_name'] = $sellerName;
        $emailTempVariables['admin'] = $adminUsername;
        $emailTempVariables['templateSubject'] = "Become a Seller";

        $senderInfo = [
            'name' => $sellerName,
            'email' => $sellerEmail,
        ];
        $receiverInfo = [
            'name' => $adminUsername,
            'email' => $adminEmail,
        ];

        $helper->sendAccountMail(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo
        );
    }
}
