<?php

namespace Netbaseteam\Marketplace\Block;

class Locator extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Marketplace\Model\LocationFactory $locationFactory,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Netbaseteam\Marketplace\Helper\Data $mpHelper,
        array $data = []
    )
    {
        $this->_locatorFactory = $locationFactory;
        $this->_sellerdataFactory = $sellerdataFactory;
        $this->mpHelper = $mpHelper;
        parent::__construct($context, $data);
    }

    protected function _getCollection()
    {
        $collection = $this->_locatorFactory->create()->getCollection();
        $filter = '';
        $data = $this->getRequest()->getPostValue();

        if (!empty($data['address'])) {
            $filter['address'] = $data['address'];
        }

        if (!empty($data['shop_title'])) {
            $filter['shop_title'] = $data['shop_title'];
        }

        if (!empty($data['shop_zipcode'])) {
            $filter['shop_zipcode'] = $data['shop_zipcode'];
        }

        if (!empty($filter['address'])) {
            $collection->addFieldToFilter('shop_location',array('like'=>'%'.$filter['address'].'%'));
        }

        if (!empty($filter['shop_title'])) {
            $sellerData = $this->_sellerdataFactory->create()->getCollection()->addFieldToFilter('shop_title',array('like'=>'%'.$filter['shop_title'].'%'));
            $sellerId = array();
            foreach ($sellerData as $data) {
                array_push($sellerId,$data->getSellerId());
            }
            $collection->addFieldToFilter('seller_id',array('in' => $sellerId));
        }

        if(!empty($filter['shop_zipcode'])) {
            $collection->addFieldToFilter('shop_zipcode',array('eq'=>$filter['shop_zipcode']));
        }

        return $collection;
    }

}
