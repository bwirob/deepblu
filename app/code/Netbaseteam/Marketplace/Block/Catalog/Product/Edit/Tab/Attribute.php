<?php

namespace Netbaseteam\Marketplace\Block\Catalog\Product\Edit\Tab;

class Attribute extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Netbaseteam\Marketplace\Model\AttributeFactory $mpAttributeFactory,
        \Netbaseteam\Marketplace\Model\AttributeSetFactory $mpAttributeSetFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        array $data = []
    ) {
        $this->collection = $collectionFactory;
        $this->mpAttributeFactory = $mpAttributeFactory;
        $this->mpAttributeSetFactory = $mpAttributeSetFactory;
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->_objectManager = $objectmanager;
        parent::__construct($context, $data);
    }

    public function getAttributes()
    {

        $attributeCodes = $this->getAttributeCodes();

        return $this->collection->create()->addFieldToFilter('attribute_code', array('in' => $attributeCodes));
    }

    public function getAttributeCodes() {

        $collection = $this->mpAttributeFactory->create()->getCollection()->addFieldToFilter('seller_id', $this->customerSession->getCustomerId());
        $attrBySeller = array();
        $attrByAttrSet = array();
        $attrbuteCodes = array();
        foreach ($collection as $item) {

            $attrBySeller[] = $item->getAttributeId();
        }

        if($this->getProduct()) {
            $product = $this->getProduct();
            $attrByAttrSet = explode(',', $this->mpAttributeSetFactory->create()->getCollection()->addFieldToFilter('attribute_set_id', $product->getAttributeSetId())->getFirstItem()->getAttributeId());
        } else {
            $attrByAttrSet = explode(',', $this->mpAttributeSetFactory->create()->getCollection()->addFieldToFilter('attribute_set_id', $this->request->getParam('set'))->getFirstItem()->getAttributeId());
        }
        $attributeIds = array_intersect($attrBySeller,$attrByAttrSet);
        foreach ($attributeIds as $id) {
            $attrbuteCodes[] = $this->mpAttributeFactory->create()->getCollection()->addFieldToFilter('attribute_id', $id)->getFirstItem()->getAttributeCode(); 
        }
        return $attrbuteCodes;
    }

    public function getProduct() {
        if($this->getRequest()->getParam('id')) {
            return $this->_objectManager->create('Magento\Catalog\Model\Product')->load($this->getRequest()->getParam('id'));
        } else {
            return null;
        }
    }

    public function getYesNoLabel($value) {
        if($value == 1) {
            return 'Yes';
        }
        return 'No';
    }
}
