<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Seller;

class EditProfile extends \Magento\Framework\View\Element\Template
{
    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory,
        \Netbaseteam\Marketplace\Model\LocationFactory $locatorFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->_customerSession = $customerSession;
        $this->_sellerdataFactory = $sellerdataFactory;
        $this->_locatorFactory = $locatorFactory;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    public function getSeller() {
        $sellerId = $this->_customerSession->getId();
        $sellerDataCollection = $this->_sellerdataFactory->create()->getCollection()
            ->addFieldToFilter('seller_id',$sellerId);

        $seller=array();
        foreach($sellerDataCollection as $data){
            array_push($seller,$data->getData());
        }
        if ($seller) {
            return $seller[0];
        } else {
            return null;
        }
    }

    public function getSellerLocator() {
        $sellerId = $this->_customerSession->getId();

        $locatorCollection = $this->_locatorFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);

        return $locatorCollection;
    }

    public function getLocatorStatusLabel($status) {
        if ($status) {
           return 'Enabled';
        }
        return 'Disabled';
    }

}
