<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\Marketplace\Block\Checkout\Onepage;

use Magento\Customer\Model\Context;
use Magento\Sales\Model\Order;
use Netbaseteam\Marketplace\Model\MarketplaceSaveCartFactory as MarketplaceSaveCartModelFactory;

/**
 * One page checkout success page
 *
 * @api
 */
class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var MarketplaceSaveCartModelFactory
     */
    private $_saveCartModelFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        MarketplaceSaveCartModelFactory $saveCartModelFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderConfig = $orderConfig;
        $this->_objectManager = $objectManager;
        $this->_saveCartModelFactory = $saveCartModelFactory;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
    }

    /**
     * Render additional order information lines and return result html
     *
     * @return string
     */
    public function getAdditionalInfoHtml()
    {
        return $this->_layout->renderElement('order.success.additional.info');
    }

    /**
     * Initialize data and prepare it for output
     *
     * @return string
     */
    protected function _beforeToHtml()
    {
        $this->prepareBlockData();
        return parent::_beforeToHtml();
    }

    /**
     * Prepares block data
     *
     * @return void
     */
    protected function prepareBlockData()
    {
        $order = $this->_checkoutSession->getLastRealOrder();

        $this->addData(
            [
                'is_order_visible' => $this->isVisible($order),
                'view_order_url' => $this->getUrl(
                    'sales/order/view/',
                    ['order_id' => $order->getEntityId()]
                ),
                'print_url' => $this->getUrl(
                    'sales/order/print',
                    ['order_id' => $order->getEntityId()]
                ),
                'can_print_order' => $this->isVisible($order),
                'can_view_order'  => $this->canViewOrder($order),
                'order_id'  => $order->getIncrementId()
            ]
        );
    }

    /**
     * Is order visible
     *
     * @param Order $order
     * @return bool
     */
    protected function isVisible(Order $order)
    {
        return !in_array(
            $order->getStatus(),
            $this->_orderConfig->getInvisibleOnFrontStatuses()
        );
    }

    /**
     * Can view order
     *
     * @param Order $order
     * @return bool
     */
    protected function canViewOrder(Order $order)
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH)
            && $this->isVisible($order);
    }

    /**
     * @return string
     * @since 100.2.0
     */

    public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
