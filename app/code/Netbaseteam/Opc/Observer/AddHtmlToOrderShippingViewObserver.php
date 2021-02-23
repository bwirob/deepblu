<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Opc\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddHtmlToOrderShippingViewObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    protected $request;

    /** @var \Magento\Sales\Model\ResourceModel\Order\Status\History $history */
    protected $history;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\Order\Status\HistoryFactory $history
    )
    {
        $this->objectManager = $objectManager;
        $this->request = $request;
        $this->history = $history;
    }

    public function execute(EventObserver $observer)
    {
        $orderId = $this->request->getParam('order_id');
        if($observer->getElementName() == 'order_shipping_view') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
            $localeDate = $this->objectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
            if($order->getDeliveryDate() != '0000-00-00 00:00:00') {
                $formattedDate = $localeDate->formatDateTime(
                    $order->getDeliveryDate(),
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::MEDIUM,
                    null,
                    $localeDate->getConfigTimezone(
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $order->getStore()->getCode()
                    )
                );
            } else {
                $formattedDate = __('N/A');
            }

            if($orderId) {
                $history = $this->history->create()->getCollection()->addFieldToFilter('parent_id', $orderId);

                foreach ($history as $item) {
                    $comment = $item->getComment();
                }
            }
            $deliveryDateBlock = $this->objectManager->create('Magento\Framework\View\Element\Template');
            $deliveryDateBlock->setDeliveryDate($formattedDate);
            if(isset($comment)) {
                $deliveryDateBlock->setComment($comment);
            }
            $deliveryDateBlock->setTemplate('Netbaseteam_Opc::order_info_shipping_info.phtml');
            $html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();
            $observer->getTransport()->setOutput($html);
        }
    }
}