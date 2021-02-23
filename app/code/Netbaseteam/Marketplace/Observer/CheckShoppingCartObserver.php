<?php
namespace Netbaseteam\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Netbaseteam\Marketplace\Api\MarketplaceSaveCartRepositoryInterface as MarketplaceSaveCartRepositoryInterface;
use Netbaseteam\Marketplace\Model\MarketplaceSaveCartFactory as MarketplaceSaveCartModelFactory;


class CheckShoppingCartObserver implements ObserverInterface
{
    protected $quoteRepository;

    protected $cart;
    /**
     * @var MarketplaceSaveCartRepositoryInterface
     */
    private $_saveCartRepository;

    /**
     * @var MarketplaceSaveCartModelFactory
     */
    private $_saveCartModelFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        MarketplaceSaveCartRepositoryInterface $saveCartRepository,
        MarketplaceSaveCartModelFactory $saveCartModelFactory,
        \Magento\Customer\Model\Session $customerSession,
        CustomerCart $cart)
    {
        $this->_saveCartRepository = $saveCartRepository;
        $this->_customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
        $this->_saveCartModelFactory = $saveCartModelFactory;
        $this->cart = $cart;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //Save Cart Data Checkout
        $checkoutSession = $this->getCheckoutSession();
        $quote = $checkoutSession->getQuote();
            if ($quote->getCustomerId() == $this->_customerSession->getCustomerId()) {
                $this->cart->setQuote($quote);
                if (!$this->cart->getCustomerSession()->getCustomerId() && $this->cart->getQuote()->getCustomerId()) {
                        $this->cart->getQuote()->setCustomerId(null);
                    }
                $savedCart = $this->_saveCartModelFactory->create();
                $savedCart->setQuoteId($quote->getId());
                $savedCart->setCustomerId($quote->getCustomerId());
                $quoteItems = $quote->getAllItems();
                $items = [];
                foreach ($quoteItems as $quoteItem)  {
                    if ($quoteItem->getParentItemId()) continue;
                    $item['id'] = $quoteItem->getId();
                    $item['qty'] = $quoteItem->getQty();
                    $item['info_buyRequest'] = $quoteItem->getOptionByCode('info_buyRequest')->getValue();
                    array_push($items, $item);
                }
                if(isset($_POST['productIds'])) {
                    $itemIdPostCheckbox = $_POST['productIds'];
                    $itemIdPostArr = explode(',', $itemIdPostCheckbox);
                    $diff = $items;
                    $diff_index = 0;
                    foreach ($itemIdPostArr as $_item) {
                        $key = array_search($_item, array_column($items, 'id'));
                        if (false !== $key) {
                            $_key = $key - $diff_index;
                            unset($diff[$key]);
                            $diff_index++;
                        }
                    }
                    $diff = serialize($diff);
                    $savedCart->setQuoteItems($diff)->save();
                }
                //Remove items not Checkbox
                $itemIds = array();
                $allItems = $checkoutSession->getQuote()->getAllVisibleItems();//returns all teh items in session
                if(isset($_POST['productIds'])){
                    $productIdCheckbox = $_POST['productIds'];
                    foreach ($allItems as $item) {
                        $itemIds[] = $item->getItemId();//item id of particular item
                    }
                    $arr= explode(',',$productIdCheckbox);
                    $result = array_diff($itemIds, $arr);
                    foreach ($result as $key=>$value){
                        $itemId = $value;
                        $this->cart->removeItem($itemId)->save();
                        }
                    }
                return $this;
            }
    }

    public function getCheckoutSession()
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
            $checkoutSession = $objectManager->get('Magento\Checkout\Model\Session');//checkout session
            return $checkoutSession;
        }

    public function getItemModel()
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
            $itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item model to load quote item
            return $itemModel;
        }
}