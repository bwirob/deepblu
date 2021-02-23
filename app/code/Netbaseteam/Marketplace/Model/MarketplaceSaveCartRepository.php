<?php

namespace Netbaseteam\Marketplace\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Netbaseteam\Marketplace\Model\MarketplaceSaveCart as MarketplaceSaveCartModel;
use Netbaseteam\Marketplace\Model\MarketplaceSaveCartFactory as MarketplaceSaveCartModelFactory;

/**
 * Class MarketplaceSaveCartRepository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MarketplaceSaveCartRepository implements \Netbaseteam\Marketplace\Api\MarketplaceSaveCartRepositoryInterface
{
    /**
     * @var MarketplaceSaveCartModel[]
     */
    protected $quotesById = [];

    /**
     * @var ResourceModel\MarketplaceSaveCart
     */
    protected $resourceModel;

    /**
     * @var MarketplaceSaveCartModelFactory
     */
    protected $saveCartModelFactory;


    /**
     * @param \Netbaseteam\Marketplace\Model\ResourceModel\MarketplaceSaveCart $resourceModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Netbaseteam\Marketplace\Model\ResourceModel\MarketplaceSaveCart $resourceModel,
        MarketplaceSaveCartModelFactory $saveCartModelFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->saveCartModelFactory = $saveCartModelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        if (!isset($this->quotesById[$cartId])) {
            $savedCart = $this->saveCartModelFactory->create();
            $this->resourceModel->load($savedCart, $cartId, 'quote_id');
            if (!$savedCart->getId()) {
                throw new NoSuchEntityException(__('Requested saved cart doesn\'t exist'));
            }

            $this->quotesById[$cartId] = $savedCart;
        }
        return $this->quotesById[$cartId];
    }

    /**
     * @inheritdoc
     */
    public function save(\Netbaseteam\Marketplace\Api\Data\MarketplaceSaveCartInterface $saveCart)
    {
        $quoteId = $saveCart->getQuoteId();
        try {
            $existingSavedCart = $this->get($quoteId);
            $existingSavedCart->setCustomerId($saveCart->getCustomerId());
        } catch (NoSuchEntityException $e) {
            $existingSavedCart = $saveCart;
        }

        unset($this->quotesById[$quoteId]);

        try {
            $this->resourceModel->save($existingSavedCart);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save cart'));
        }

        return $this->get($quoteId);
    }

    /**
     * @inheritdoc
     */
    public function isQuoteSaved($cartId)
    {
        try {
            return $this->get($cartId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(\Netbaseteam\Marketplace\Api\Data\MarketplaceSaveCartInterface $saveCart)
    {
        unset($this->quotesById[$saveCart->getId()]);

        try {
            $this->resourceModel->delete($saveCart);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove saved cart')
            );
        }

        return true;
    }
}
