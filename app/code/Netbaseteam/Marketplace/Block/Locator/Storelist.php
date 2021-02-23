<?php

namespace Netbaseteam\Marketplace\Block\Locator;

class Storelist extends \Netbaseteam\Marketplace\Block\Locator
{
    public function getStoresData()
    {
        $collection = $this->_getCollection();
        $collection->addFieldToFilter('status', array('eq' => '1'))
            ->setOrder('id', 'ASC');

        return $collection->getData();
    }

    public function getSellerData($sellerId)
    {
        $sellerId = $sellerId;
        $collection = $this->_sellerdataFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);
        return $collection->getData();
    }

    public function toPositionJson($store)
    {
        $position = array();
        $position['lat'] = floatval($store['shop_latitude']);
        $position['lng'] = floatval($store['shop_longitude']);
        $position = json_encode($position);
        return $position;

    }

    public function toStoreJsonData($id)
    {
        $storeInfo = array();
        if ($id) {
            $storeInfo['store_id'] = intval($id);

            $subStoreModel = $this->_locatorFactory->create()->load($storeInfo['store_id']);
            $storeInfo['address'] = $subStoreModel->getShopLocation();

            $sellerId = $subStoreModel->getSellerId();
            $sellerData = $this->getSellerData($sellerId);

            if (isset($sellerData[0]['shop_title'])) {
                $storeInfo['store_name'] = $sellerData[0]['shop_title'];
            }
            $yourShopID = '';
            if (isset($sellerData[0]['shop_id'])) {
                $yourShopID = $sellerData[0]['shop_id'];
                $storeInfo['store_link'] = $this->getUrl("marketplace/seller/profile/shop/$yourShopID");
            }
            if (isset($sellerData[0]['contact_number'])) {
                $storeInfo['phone_number'] = $sellerData[0]['contact_number'];
            }
            if (isset($sellerData[0]['shop_logo'])) {
                $storeInfo['logo'] = $this->mpHelper->getMediaUrl() . 'marketplace/' . $sellerData[0]['shop_logo'];
            }
        }

        $storeInfo = json_encode($storeInfo);
        return $storeInfo;

    }
}