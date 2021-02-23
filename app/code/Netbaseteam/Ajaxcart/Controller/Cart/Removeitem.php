<?php
namespace Netbaseteam\Ajaxcart\Controller\Cart;

/* Magento\Checkout\Controller\Sidebar\RemoveItem */
class Removeitem extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Sidebar
     */
    protected $sidebar;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

	protected $_blockCart;
	protected $_layoutFactory;

    protected $_resultJsonFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Sidebar $sidebar
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Sidebar $sidebar,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Checkout\Block\Cart\Totals $blockCart,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->sidebar = $sidebar;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->_blockCart = $blockCart;
		$this->_layoutFactory = $layoutFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
		$json_encode = array();
        $itemId = (int)$this->getRequest()->getParam('item_id');
        $error = 0; $msg_error = "";
		try {
            $this->sidebar->checkQuoteItem($itemId);
            $this->sidebar->removeQuoteItem($itemId);
			$json_encode["sub_total_html"] = $this->_blockCart->renderTotals();
			$json_encode["items_count"] = $this->_blockCart->getQuote()->getItemsCount();
			$grand_total = $this->_blockCart->displayBaseGrandtotal();
			$json_encode["grand_total_html"] = $this->_layoutFactory->create()
						->createBlock('Magento\Checkout\Block\Cart\Totals')
						->setData('grand_total', $grand_total)
						->setTemplate('Netbaseteam_Ajaxcart::module-checkout/cart/totals/grand-total.phtml')
						->toHtml();
						
			/* return $this->jsonResponse(); */
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
			$error = 1;
			$msg_error = $e->getMessage();
		   /*  return $this->jsonResponse($e->getMessage()); */
        } catch (\Exception $e) {
			$error = 1;
			$msg_error = $e->getMessage();
            /* $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage()); */
        }
		$json_encode["error"] = $error;
		$json_encode["error_msg"] = $msg_error;
		$result = $this->_resultJsonFactory->create();
        return $result->setData($json_encode);
    }

    /**
     * Compile JSON response
     *
     * @param string $error
     * @return \Magento\Framework\App\Response\Http
     */
    protected function jsonResponse($error = '')
    {
        $response = $this->sidebar->getResponseData($error);

        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
