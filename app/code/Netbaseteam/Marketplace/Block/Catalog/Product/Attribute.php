<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Catalog\Product;

class Attribute extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory
     */
    protected $_inputTypeFactory;

    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Netbaseteam\Marketplace\Model\AttributeFactory $mpAttributeFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    )
    {
        $this->_inputTypeFactory = $inputTypeFactory;
        $this->_objectManager = $objectmanager;
        $this->_attributeFactory = $attributeFactory;
        $this->mpAttributeFactory = $mpAttributeFactory;
        $this->customerSession = $customerSession;
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    public function catalogInputType() {
        return $this->_inputTypeFactory->create()->toOptionArray();
    }

    public function getAttributeDetails() {
        if($this->getRequest()->getParam('isAjax')){
            $attrIds = $this->getRequest()->getParam('attr');
            if($attrIds) {
                return $this->_attributeFactory->create()->getCollection()->addFieldToFilter('attribute_id', $attrIds);
            } else {
                return null;
            }
        }
    }

    public function getListAttributes() {
        $sellerId = $this->customerSession->getCustomerId();
        $collection = $this->mpAttributeFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);
        return $collection;
    }
    
    public function getListAttributesDetails($attrId) {
        return $this->_attributeFactory->create()->getCollection()->addFieldToFilter('attribute_id', $attrId);
    }

    public function getProduct() {
        if($this->getRequest()->getParam('pid')) {
            return $this->_objectManager->create('Magento\Catalog\Model\Product')->load($this->getRequest()->getParam('pid'));
        } else {
            return null;
        }
    }

    public function getAttibuteOptions($attrCode) {
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attrCode);
        $options = $attribute->getSource()->getAllOptions();

        return $options;
    }
}
