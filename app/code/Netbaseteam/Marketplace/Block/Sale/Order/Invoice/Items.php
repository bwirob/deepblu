<?php

namespace Netbaseteam\Marketplace\Block\Sale\Order\Invoice;

/**
 * @api
 * @since 100.0.2
 */
class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Marketplace\Model\OrderFactory $mpOrderFactory,
        \Netbaseteam\Marketplace\Helper\Data $mpHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_mpOrderFactory = $mpOrderFactory;
        $this->_mpHelper = $mpHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    public function getProductIds()
    {
        $productIds = array();
        $sellerId = $this->_mpHelper->getSellerId();
        $orderId = $this->getOrder()->getId();

        $mpOrder = $this->_mpOrderFactory->create()->getCollection()
                    ->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('seller_id', $sellerId)
                    ->getFirstItem();
        if($mpOrder->getData()) {
            $productIds = explode(',', $mpOrder->getProductIds());
        }             
        $productIds = explode(',', $mpOrder->getProductIds());

        return $productIds;
    }

    /**
     * @param object $invoice
     * @return string
     */
    public function getPrintInvoiceUrl($invoice)
    {
        return $this->getUrl('*/*/printInvoice', ['invoice_id' => $invoice->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getPrintAllInvoicesUrl($order)
    {
        return $this->getUrl('*/*/printInvoice', ['order_id' => $order->getId()]);
    }

    /**
     * Get html of invoice totals block
     *
     * @param   \Magento\Sales\Model\Order\Invoice $invoice
     * @return  string
     */
    public function getInvoiceTotalsHtml($invoice)
    {
        $html = '';
        $totals = $this->getChildBlock('invoice_totals');
        if ($totals) {
            $totals->setInvoice($invoice);
            $html = $totals->toHtml();
        }
        return $html;
    }

    /**
     * Get html of invoice comments block
     *
     * @param   \Magento\Sales\Model\Order\Invoice $invoice
     * @return  string
     */
    public function getInvoiceCommentsHtml($invoice)
    {
        $html = '';
        $comments = $this->getChildBlock('invoice_comments');
        if ($comments) {
            $comments->setEntity($invoice)->setTitle(__('About Your Invoice'));
            $html = $comments->toHtml();
        }
        return $html;
    }
}
