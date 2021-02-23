<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Marketplace Product Verifysku controller.
 * Verify SKU If avialable or not.
 */
class Checksku extends Action
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $_productResourceModel;

    /**
     * @param \Magento\Framework\App\Action\Context        $context
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel
    ) {
        $this->_productResourceModel = $productResourceModel;
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
        $skuPrefix = $helper->getSkuPrefix();
        $isPartner = $helper->getSellerId();
        if ($isPartner) {
            $sku = $this->getRequest()->getParam('sku');
            $sku = $skuPrefix.$sku;
            try {
                $id = $this->_productResourceModel->getIdBySku($sku);
                if ($id) {
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
