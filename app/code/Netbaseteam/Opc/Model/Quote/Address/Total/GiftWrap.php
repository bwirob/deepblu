<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Opc\Model\Quote\Address\Total;

class GiftWrap extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency [description]
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    )
    {
        $this->_priceCurrency = $priceCurrency;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);

        $giftWrap = $quote->getGiftwrapAmount();
        if($giftWrap) {
            $total->addTotalAmount('giftwrap', $giftWrap);
            $total->addBaseTotalAmount('giftwrap', $giftWrap);
            $quote->setGiftWrap($giftWrap);
        }
        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return [
            'code' => 'giftwrap',
            'title' => $this->getLabel(),
            'value' => $quote->getGiftwrapAmount()
        ];
    }

    /**
     * get label
     * @return string
     */
    public function getLabel()
    {
        return __('Gift Wrap');
    }
}