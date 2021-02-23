<?php

namespace Netbaseteam\Marketplace\Controller\Product\Attribute;

/**
 * Marketplace Product GetAttributes controller.
 */
class GetAttributes extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\ConfigurableProduct\Model\AttributesList
     */
    protected $_configurableAttributesList;

    /**
     * @param \Magento\Framework\App\Action\Context             $context
     * @param \Magento\ConfigurableProduct\Model\AttributesList $configurableAttributesList
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\ConfigurableProduct\Model\AttributesList $configurableAttributesList
    ) {
        $this->_configurableAttributesList = $configurableAttributesList;
        parent::__construct($context);
    }

    /**
     * Get Eav Attributes action.
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        if ($isPartner) {
            $attributesArray = $this->_configurableAttributesList
            ->getAttributes($this->getRequest()->getParam('attributes'));
            $this->getResponse()->representJson(
                $this->_objectManager->get(
                    'Magento\Framework\Json\Helper\Data'
                )->jsonEncode($attributesArray)
            );
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/registry',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
