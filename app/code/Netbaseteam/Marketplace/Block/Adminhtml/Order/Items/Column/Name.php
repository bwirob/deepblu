<?php

namespace Netbaseteam\Marketplace\Block\Adminhtml\Order\Items\Column;

class Name extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    public function getSeller($productId)
    {
        $sellerId = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $mpProductCollection = $objectManager->get(
            'Netbaseteam\Marketplace\Model\Product'
        )
            ->getCollection()
            ->addFieldToFilter('product_id',$productId);
        if (count($mpProductCollection)) {
            foreach ($mpProductCollection as $item) {
                $sellerId = $item->getSellerId();
            }
        }

        if ($sellerId > 0) {
            $customer = $objectManager->get('Magento\Customer\Model\Customer')->load($sellerId);
            if ($customer) {
                $sellerArray = [];
                $sellerArray['name'] = $customer->getName();
                $sellerArray['id'] = $sellerId;

                return $sellerArray;
            }
        }
    }

    /**
     * Get Customer Url By Customer Id.
     *
     * @param string | $customerId
     *
     * @return string
     */
    public function getCustomerUrl($customerId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $urlbuilder = $objectManager->get(
            'Magento\Framework\UrlInterface'
        );

        return $urlbuilder->getUrl(
            'customer/index/edit',
            ['id' => $customerId]
        );
    }
}
