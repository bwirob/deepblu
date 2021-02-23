<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Vacation;

class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Marketplace\Model\Config\Source\Vacation\DisabletypeFactory $disableTypeFactory,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->storeManager = $context->getStoreManager();;
        $this->_disableTypeFactory = $disableTypeFactory;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    public function getDisableType() {
        return $this->_disableTypeFactory->create()->toOptionArray();
    }

    public function getVacation() {
        $vacation = $this->_registry->registry('vacation');
        if (!empty($vacation)) {
            $vacationData = $vacation->getData();
            if (!empty($vacationData)) {
                return $vacationData[0];
            } else {
                return null;
            }
        }

        return null;
    }
}
