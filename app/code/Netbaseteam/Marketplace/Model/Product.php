<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Netbaseteam\Marketplace\Model\ResourceModel\Product as ResourceProduct;

class Product extends \Magento\Framework\Model\AbstractModel
{

    /**#@+
     * Product's Statuses
     */
    const STATUS_PENDING = 0;
    const STATUS_APPROVE = 1;
    const STATUS_DISAPPROVE = 2;
    
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'marketplace_product';

    /**
     * @var string
     */
    protected $_cacheTag = 'marketplace_product';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_product';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Netbaseteam\Marketplace\Model\ResourceModel\Product');
    }

    /**
     * Prepare grid's statuses.
     * Available event product_grid_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_PENDING => __('Pending'),self::STATUS_APPROVE => __('Approve'), self::STATUS_DISAPPROVE => __('Disapprove')];
    }
}
