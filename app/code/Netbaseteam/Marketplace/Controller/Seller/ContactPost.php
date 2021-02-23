<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Seller;

use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class ContactPost extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Netbaseteam\Marketplace\Helper\Data $marketplaceHelperData
    )
    {
        $this->_registry = $registry;
        $this->_dateFactory = $dateFactory;
        $this->_sellerdataFactory = $sellerdataFactory;
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

        $shopId = $this->getRequest()->getParam('shop');

        $sellerDataCollection = $this->_sellerdataFactory->create()->getCollection()
            ->addFieldToFilter('shop_id',$shopId);

        $shopData = '';
        foreach ($sellerDataCollection as $item) {
            $shopData = $item->getData();
        }

        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $this->sendContactMail($data, $shopData);
                $this->messageManager->addSuccess(__('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while sending the email.'));
            }
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        } else {
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }
    }

    /**
     * @param array $data
     * @param string $sellerId
     */
    private function sendContactMail($data, $shopData)
    {
        $helper = $this->_marketplaceHelperData;
        $shopEmail = '';
        $shopName = '';
        if ($shopData) {
            $shopEmail = $shopData['shop_email'];
            $shopName = $shopData['shop_title'];
        }
        if (!$shopEmail) {
            $sellerId = $shopData['seller_id'];
            if ($sellerId) {
                $customer = $this->_objectManager->get(
                    'Magento\Customer\Model\Customer'
                )->load($sellerId);

                $shopName = $customer->getFirstname() . ' ' . $customer->getLastname();
                $shopEmail = $customer->getEmail();
            }
        }

        $emailTempVariables = [];
        $customerEmail = $data['email'];
        $customerName = $data['name'];

        $emailTempVariables['admin'] = $customerName;
        $emailTempVariables['templateSubject'] = 'Contact';
        $emailTempVariables['infomation'] = $data['comment'];
        $emailTempVariables['telephone'] = __("My phone number: ".$data['telephone']);

        $senderInfo = [
            'name' => $customerName,
            'email' => $customerEmail,
        ];
        $receiverInfo = [
            'name' => $shopName,
            'email' => $shopEmail,
        ];

        $helper->sendContactMail(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo
        );
    }
}
