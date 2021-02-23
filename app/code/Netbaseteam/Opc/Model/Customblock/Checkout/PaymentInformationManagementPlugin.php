<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Opc\Model\Customblock\Checkout;

class PaymentInformationManagementPlugin
{

    /** @var \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory */
    protected $historyFactory;
    /** @var \Magento\Sales\Model\OrderFactory $orderFactory */
    protected $orderFactory;

    protected $quoteFactory;

    /**
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\GiftMessage\Model\MessageFactory $messageFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    )
    {
        $this->historyFactory = $historyFactory;
        $this->orderFactory = $orderFactory;
        $this->messageFactory = $messageFactory;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     *
     * @return int $orderId
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    )
    {

        /** @param string $comment */
        $orderComments = null;
        $orderDelivery = null;

        $messageCollection = $this->messageFactory->create()->getCollection();
        if ($messageCollection) {
            foreach ($messageCollection as $item) {
                $messageId[] = $item->getId();
            }
            if(isset($messageId)) {
                $lastMessageId = array_pop($messageId);
            } else {
                $lastMessageId = 0;
            }
        } else {
            $lastMessageId = 0;
        }


        // get JSON post data
        $request_body = file_get_contents('php://input');
        // decode JSON post data into array
        $data = json_decode($request_body, true);

        // get order delivery
        if (isset ($data['paymentMethod']['additional_data']['delivery'])) {
            // make sure there is a comment to save
            $orderDelivery = $data['paymentMethod']['additional_data']['delivery'];
        }

        // get order comments
        if (isset ($data['paymentMethod']['additional_data']['comments'])) {
            // make sure there is a comment to save
            $orderComments = $data['paymentMethod']['additional_data']['comments'];
        }

        // get order message
        if (isset ($data['paymentMethod']['additional_data']['messageTo'])) {
            // make sure there is a messageTo to save
            $orderMessageTo = $data['paymentMethod']['additional_data']['messageTo'];
        }
        if (isset ($data['paymentMethod']['additional_data']['messageFrom'])) {
            // make sure there is a messageFrom to save
            $orderMessageFrom = $data['paymentMethod']['additional_data']['messageFrom'];
        }
        if (isset ($data['paymentMethod']['additional_data']['message'])) {
            // make sure there is a message to save
            $orderMessage = $data['paymentMethod']['additional_data']['message'];
        }

        // run parent method and capture int $orderId
        $orderId = $proceed($cartId, $paymentMethod, $billingAddress);
        /** @param \Magento\Sales\Model\OrderFactory $order */
        $order = $this->orderFactory->create()->load($orderId);

        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);

        if ($order->getData('entity_id')) {
            //if $orderDelivery
            if (isset($orderDelivery)) {
                $order->setDeliveryDate($orderDelivery);
                $order->save();
            }

            // if $orderComments
            if (isset($orderComments)) {
                // make sure $order exists
                /** @param string $status */
                $status = $order->getData('status');
                /** @param \Magento\Sales\Model\Order\Status\HistoryFactory $history */
                $history = $this->historyFactory->create();
                // set comment history data
                $history->setData('comment', $orderComments);
                $history->setData('parent_id', $orderId);
                $history->setData('is_visible_on_front', 1);
                $history->setData('is_customer_notified', 0);
                $history->setData('entity_name', 'order');
                $history->setData('status', $status);
                $history->save();
            }

            if (isset($orderMessageTo)) {
                $order->setData('gift_message_id', $lastMessageId + 1);
                $order->save();

                if ($quote->getData('entity_id')) {
                    $quote->setData('gift_message_id', $lastMessageId + 1);
                    $quote->save();
                }

                $message = $this->messageFactory->create();
                $message->load($lastMessageId + 1);

                $message->setData('recipient', $orderMessageTo);
                $message->setCustomerId($quote->getCustomerId());
                $message->save();
            }
            if (isset($orderMessageFrom)) {
                $order->setData('gift_message_id', $lastMessageId + 1);
                $order->save();

                if ($quote->getData('entity_id')) {
                    $quote->setData('gift_message_id', $lastMessageId + 1);
                    $quote->save();
                }

                $message = $this->messageFactory->create();
                $message->load($lastMessageId + 1);

                $message->setData('sender', $orderMessageFrom);
                $message->setCustomerId($quote->getCustomerId());
                $message->save();
            }
            if (isset($orderMessage)) {
                $order->setData('gift_message_id', $lastMessageId + 1);
                $order->save();

                if ($quote->getData('entity_id')) {
                    $quote->setData('gift_message_id', $lastMessageId + 1);
                    $quote->save();
                }

                $message = $this->messageFactory->create();
                $message->load($lastMessageId + 1);

                $message->setData('message', $orderMessage);
                $message->setCustomerId($quote->getCustomerId());
                $message->save();

            }
        }


        return $orderId;
    }
}