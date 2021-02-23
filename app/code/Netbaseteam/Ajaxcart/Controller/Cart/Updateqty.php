<?php

namespace Netbaseteam\Ajaxcart\Controller\Cart;

use Magento\Checkout\Model\Sidebar;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data;
use Psr\Log\LoggerInterface;

class Updateqty extends Action
{
    /**
     * @var Sidebar
     */
    protected $sidebar;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $jsonHelper;
	protected $_blockCart;
	protected $_layoutFactory;

    protected $_resultJsonFactory;

    /**
     * @param Context $context
     * @param Sidebar $sidebar
     * @param LoggerInterface $logger
     * @param Data $jsonHelper
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Sidebar $sidebar,
        LoggerInterface $logger,
		\Magento\Checkout\Block\Cart\Totals $blockCart,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Data $jsonHelper
    ) {
        $this->sidebar = $sidebar;
        $this->logger = $logger;
        $this->_layoutFactory = $layoutFactory;
        $this->jsonHelper = $jsonHelper;
		$this->_blockCart = $blockCart;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('utype');
		$json_encode = array();
        try {
			if($type == "single-update-qty"){
				$itemId = (int)$this->getRequest()->getParam('item_id');
				$itemQty = (int)$this->getRequest()->getParam('item_qty');
			
				$this->sidebar->checkQuoteItem($itemId);
				$this->sidebar->updateQuoteItem($itemId, $itemQty);
			} elseif($type == "multi-update-qty"){
				$item_id_array = explode(",", $this->getRequest()->getParam('item_id'));
				$item_qty_array = explode(",", $this->getRequest()->getParam('item_qty'));
				for($i=0; $i<count($item_id_array); $i++){
					$itemId = $item_id_array[$i];
					if($itemId != ""){
						$this->sidebar->checkQuoteItem($itemId);
						$this->sidebar->updateQuoteItem($itemId, $item_qty_array[$i]);
					}
				}
			}
			
            $json_encode["error"] = 0;
			$json_encode["sub_total_html"] = $this->_blockCart->renderTotals();
			/* render Order Total block on checkout/cart  */
			$grand_total = $this->_blockCart->displayBaseGrandtotal();
			$json_encode["grand_total_html"] = $this->_layoutFactory->create()
						->createBlock('Magento\Checkout\Block\Cart\Totals')
						->setData('grand_total', $grand_total)
						->setTemplate('Netbaseteam_Ajaxcart::module-checkout/cart/totals/grand-total.phtml')
						->toHtml();
	
        } catch (LocalizedException $e) {
			$json_encode["error"] = 1;
            $json_encode["error_msg"] = $e->getMessage();
        } catch (\Exception $e) {
			$json_encode["error"] = 1;
			$json_encode["error_msg"] = $e->getMessage();
        }
		
		$result = $this->_resultJsonFactory->create();
        return $result->setData($json_encode);
    }

    /**
     * Compile JSON response
     *
     * @param string $error
     * @return Http
     */
    protected function jsonResponse($error = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($this->sidebar->getResponseData($error))
        );
    }
}
