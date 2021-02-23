<?php

namespace Netbaseteam\Marketplace\Block;

class Vendor extends \Magento\Framework\View\Element\Template
{

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Marketplace\Model\Sellerdata $sellerData,
        \Magento\Customer\Model\Session $customerSession,
        \Netbaseteam\Marketplace\Helper\Data $helper,
        \Netbaseteam\Marketplace\Model\ResourceModel\Notification\CollectionFactory $notificationCollectionFactory,
        array $data = []
    )
    {
        $this->_sellerData = $sellerData;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_notificationCollectionFactory = $notificationCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->setKeywords($this->getShopData()['meta_keyword']);
        $this->pageConfig->setDescription($this->getShopData()['meta_description']);
        parent::_prepareLayout();
    }

    public function getShopData()
    {
        $shopID = $this->currentShopID();
        $sellerDataCollection = $this->_sellerData->getCollection()->addFieldToFilter('shop_id',$shopID);

        $sellerData=array();
        foreach($sellerDataCollection as $data){
            array_push($sellerData,$data->getData());
        }
        if ($sellerData) {
            return $sellerData[0];
        } else {
            return null;
        }
    }

    public function currentShopID () {
        $shopID = '';
        $sellerId = '';
        $isPartner = $this->_helper->getSellerId();
        if ($isPartner) {
            $sellerId = $this->_customerSession->getCustomerId();
            $sellerData = $this->_sellerData->getCollection()->addFieldToFilter('seller_id',$sellerId);

            foreach ($sellerData as $seller) {
                $shopID = $seller->getShopId();
            }
        }
        return $shopID;
    }

    public function getCustomer() {
        return $this->_customerSession->getCustomer();
    }

    public function getNotifications () {
        $sellerId = $this->_customerSession->getCustomerId();
        return $this->_notificationCollectionFactory->create()->addFieldToFilter('seller_id',$sellerId)->addFieldToFilter('is_read',0);
    }
}
