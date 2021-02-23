<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Account;

class Registry extends \Magento\Framework\View\Element\Template
{
    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Marketplace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->sellerFactory = $sellerFactory;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {

        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Become a Seller'));
        return $this;
    }

    public function isRequest() {
        $collection = $this->sellerFactory->create()->getCollection();

        $customerIds=array();
        foreach($collection as $data){
            array_push($customerIds,$data->getSellerId());
        }
        $sellerId = $this->_registry->registry('current_customer');
        if (in_array($sellerId,$customerIds)) {
            return true;
        } else {
            return false;
        }
    }

}
