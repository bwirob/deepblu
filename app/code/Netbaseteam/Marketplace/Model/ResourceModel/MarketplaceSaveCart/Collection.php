<?php


namespace Netbaseteam\Marketplace\Model\ResourceModel\MarketplaceSaveCart;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Netbaseteam\Marketplace\Model\MarketplaceSaveCart', 'Netbaseteam\Marketplace\Model\ResourceModel\MarketplaceSaveCart');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('entity_id', 'title');
    }
}
