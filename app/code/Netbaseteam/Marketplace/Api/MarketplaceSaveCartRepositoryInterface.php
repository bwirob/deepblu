<?php

namespace Netbaseteam\Marketplace\Api;

/**
 * Interface MarketplaceSaveCartRepositoryInterface
 * @api
 */
interface MarketplaceSaveCartRepositoryInterface
{
    /**
     * Enables an administrative user to return information for a specified cart.
     *
     * @param int $cartId
     * @return Data\MarketplaceSaveCartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($cartId);

    /**
     * @param Data\MarketplaceSaveCartInterface $saveCart
     * @return Data\MarketplaceSaveCartInterface
     */
    public function save(Data\MarketplaceSaveCartInterface $saveCart);

    /**
     * @param Data\MarketplaceSaveCartInterface $saveCart
     * @return void
     */
    public function delete(Data\MarketplaceSaveCartInterface $saveCart);

    /**
     * @param int $cartId
     * @return bool|Data\MarketplaceSaveCartInterface
     */
    public function isQuoteSaved($cartId);
}
