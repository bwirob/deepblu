<?php
namespace Netbaseteam\Orderupload\Model\Plugin\Quote;

use Closure;

class Quotetoorderitem
{
    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return \Magento\Sales\Model\Order\Item
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
  public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);//result of function 'convert' in class 'Magento\Quote\Model\Quote\Item\ToOrderItem' 
        $orderItem->setSessionFile($item->getSessionFile());//set your required
        return $orderItem;// return an object '$orderItem' which will replace result of function 'convert' in class 'Magento\Quote\Model\Quote\Item\ToOrderItem'
    }

}