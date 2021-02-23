<?php

namespace Netbaseteam\Navigation\Block\Product\ProductList;

use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    protected $_salesResource;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param ToolbarModel $toolbarModel
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param ProductList $productListHelper
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Config $catalogConfig,
        ToolbarModel $toolbarModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        ProductList $productListHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Sales\Model\ResourceModel\Order\Item $item,
        \Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\Decimal $decimal,
        \Magento\Review\Model\ResourceModel\Rating\Option $rating,
        \Magento\Review\Model\ResourceModel\Review\Summary $review,
        array $data = []
    )
    {
        $this->_catalogSession = $catalogSession;
        $this->_catalogConfig = $catalogConfig;
        $this->_toolbarModel = $toolbarModel;
        $this->urlEncoder = $urlEncoder;
        $this->_productListHelper = $productListHelper;
        $this->_postDataHelper = $postDataHelper;
        $this->_salesResource = $item;
        $this->_promoResource = $decimal;
        $this->_rating = $rating;
        $this->_review = $review;
        parent::__construct($context, $catalogSession, $catalogConfig, $toolbarModel, $urlEncoder, $productListHelper, $postDataHelper, $data);
    }

    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {

        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }


        // switch tra i tipi di ordinamento

        // echo '<pre>';
        // var_dump($this->getAvailableOrders());
        // die;

        if ($this->getCurrentOrder()) {


            // Costruisco la custom query
            switch ($this->getCurrentOrder()) {

                case 'created_at':

                    if ($this->getCurrentDirection() == 'desc') {

                        $this->_collection
                            ->getSelect()
                            ->order('e.created_at DESC');


                    } elseif ($this->getCurrentDirection() == 'asc') {

                        $this->_collection
                            ->getSelect()
                            ->order('e.created_at ASC');

                    }

                    break;

                case 'best_seller':

                    if ($this->getCurrentDirection() == 'desc') {

                        $this->_collection
                            ->getSelect()
                            ->joinLeft(['soi' => $this->_salesResource->getMainTable()], 'e.entity_id =soi.product_id', 'SUM(soi.qty_ordered) AS ordered_qty')
                            ->group('e.entity_id')->order('ordered_qty DESC');


                    } elseif ($this->getCurrentDirection() == 'asc') {

                        $this->_collection
                            ->getSelect()
                            ->joinLeft(['soi' => $this->_salesResource->getMainTable()], 'e.entity_id =soi.product_id', 'SUM(soi.qty_ordered) AS ordered_qty')
                            ->group('e.entity_id')->order('ordered_qty ASC');

                    }

                    break;

                case 'promo_product':

                    if ($this->getCurrentDirection() == 'desc') {

                        $this->_collection
                            ->getSelect()
                            ->joinLeft(['cped' => $this->_promoResource->getMainTable()], 'e.entity_id = cped.entity_id')
                            ->where('cped.attribute_id = 78')
                            ->order('e.entity_id DESC');

                    } elseif ($this->getCurrentDirection() == 'asc') {

                        $this->_collection
                            ->getSelect()
                            ->joinLeft(['cped' => $this->_promoResource->getMainTable()], 'e.entity_id = cped.entity_id')
                            ->where('cped.attribute_id = 78')
                            ->order('e.entity_id ASC');
                    }

                    break;

                case 'rating':

                    if ($this->getCurrentDirection() == 'desc') {
                        $this->_collection
                            ->getSelect()
                            ->joinLeft(['rova' => $this->_rating->getTable('rating_option_vote_aggregated')], 'e.entity_id =rova.entity_pk_value')
                            ->group('e.entity_id')
                            ->order('rova.percent DESC');

                    } elseif ($this->getCurrentDirection() == 'asc') {

                        $this->_collection
                            ->getSelect()
                            ->joinLeft(['rova' => $this->_rating->getTable('rating_option_vote_aggregated')], 'e.entity_id =rova.entity_pk_value')
                            ->group('e.entity_id')
                            ->order('rova.percent DESC');
                    }

                    break;

                case 'review':

                    if ($this->getCurrentDirection() == 'desc') {

                        $this->_collection
                            ->getSelect()
                            ->joinLeft(['res' => $this->_review->getMainTable()], 'e.entity_id =res.entity_pk_value')
                            ->group('e.entity_id')
                            ->order('res.reviews_count DESC');

                    } elseif ($this->getCurrentDirection() == 'asc') {

                        $this->_collection
                            ->getSelect()
                            ->joinLeft(['res' => $this->_review->getMainTable()], 'e.entity_id =res.entity_pk_value')
                            ->group('e.entity_id')
                            ->order('res.reviews_count ASC');
                    }

                    break;

                default:

                    $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
                    break;

            }

        }

        return $this;

    }

}