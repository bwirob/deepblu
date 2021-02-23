<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Marketplace\Controller\Notification;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class AjaxMarkAsRead extends Action
{
    public function __construct(
        Context $context,
        \Netbaseteam\Marketplace\Model\NotificationFactory $notificationFactory
    ) {
        $this->_notificationFactory = $notificationFactory;
        parent::__construct($context);
    }

    /**
     * Mark notification as read (AJAX action)
     *
     * @return \Magento\Framework\Controller\Result\Json|void
     * @throws \InvalidArgumentException
     */
    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            return;
        }
        $notificationId = (int)$this->getRequest()->getPost('id');
        $responseData = [];
        try {
            $this->markAsRead($notificationId);
            $responseData['success'] = true;
        } catch (\Exception $e) {
            $responseData['success'] = false;
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;
    }

    public function markAsRead($notificationId)
    {
        $notification = $this->_notificationFactory->create();
        $notification->load($notificationId);
        if (!$notification->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Wrong notification ID specified.'));
        }
        $notification->setIsRead(1);
        $notification->save();
    }
}
