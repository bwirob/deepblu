<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Seller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Marketplace Product Verifysku controller.
 * Verify SKU If avialable or not.
 */
class Checkshopid extends Action
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $_productResourceModel;

    /**
     * @param \Magento\Framework\App\Action\Context        $context
     * @param \Netbaseteam\Marketplace\Model\Sellerdata        $sellerData
     */
    public function __construct(
        Context $context,
        \Netbaseteam\Marketplace\Model\Sellerdata $sellerData,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_sellerData = $sellerData;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Verify Product SKU availability action.
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        if ($isPartner) {
            $shopId = $this->getRequest()->getParam('shop_id');
            $sellerId = $this->_customerSession->getCustomerId();
            try {
                $id1 = $this->_sellerData->getCollection()->addFieldToFilter('shop_id',$shopId)->getAllIds();
                $id2 = $this->_sellerData->getCollection()->addFieldToFilter('seller_id',$sellerId)->getAllIds();
                if ($id1 && $id1 != $id2) {
                    $avialability = 0;
                } else {
                    $avialability = 1;
                }
                $this->getResponse()->representJson(
                    $this->_objectManager->get(
                        'Magento\Framework\Json\Helper\Data'
                    )
                    ->jsonEncode(
                        ['avialability' => $avialability]
                    )
                );
            } catch (\Exception $e) {
                $this->getResponse()->representJson(
                    $this->_objectManager->get(
                        'Magento\Framework\Json\Helper\Data'
                    )
                    ->jsonEncode('')
                );
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/registry',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
