<?php

namespace Netbaseteam\Marketplace\Api\Data;

/**
 * @api
 */
interface MarketplaceSaveCartInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int|null
     */
    public function getQuoteId();

    /**
     * @param int $id
     * @return $this
     */
    public function setQuoteId($id);

    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @param int $id
     * @return $this
     */
    public function setCustomerId($id);

    /**
     * Returns the cart creation date and time.
     *
     * @return string|null Cart creation date and time. Otherwise, null.
     */
    public function getCreatedAt();

    /**
     * Returns the cart last update date and time.
     *
     * @return string|null Cart last update date and time. Otherwise, null.
     */
    public function getUpdatedAt();
}
