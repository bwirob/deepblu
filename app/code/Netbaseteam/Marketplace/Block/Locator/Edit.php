<?php

namespace Netbaseteam\Marketplace\Block\Locator;

class Edit extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        array $data = []
    )
    {
        $this->_objectManager = $objectManagerInterface;
        parent::__construct($context, $data);
    }

    public function getLocator()
    {
        $id = $this->getRequest()->getParam('id');
        if($id) {
            $model = $this->_objectManager->create(
                'Netbaseteam\Marketplace\Model\Location'
            )->load($id);

            return $model;
        }

        return null;
    }

}
