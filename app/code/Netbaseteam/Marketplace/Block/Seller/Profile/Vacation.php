<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Block\Seller\Profile;

class Vacation extends \Magento\Framework\View\Element\Template
{
    /**
     * Reward constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Marketplace\Model\Sellerdata $sellerData,
        \Magento\Customer\Model\Session $customerSession,
        \Netbaseteam\Marketplace\Model\VacationFactory $vacationFactory,
        \Netbaseteam\Marketplace\Helper\Data $mpHelper,
        array $data = []
    )
    {
        $this->_customerSession = $customerSession;
        $this->_sellerData = $sellerData;
        $this->_vacationFactory = $vacationFactory;
        $this->_mpHelper = $mpHelper;
        parent::__construct($context, $data);
    }

    public function getVacation() {
        $isVacation = $this->_mpHelper->isVacation();

        if ($isVacation == 1) {
            $sellerID  = $this->getSellerId();

            $vaCollection = $this->_vacationFactory
                ->create()->getCollection()
                ->addFieldToFilter('vacation_status', 1)
                ->addFieldToFilter('seller_id', $sellerID);
            $vacationData = '';
            if(!empty($vaCollection->getData())) {
                $vacationData = $vaCollection->getData()[0];
            }

            if (!empty($vacationData)) {
                $dateNow = date_create(date('m/d/Y h:i:s a', time()));
                $dateFrom = date_create($vacationData['date_from']);
                $dateTo = date_create($vacationData['date_to']);

                $diff = (int)date_diff($dateFrom, $dateNow)->format("%R%h%i%s%a");
                if ($vacationData['date_to'] && $diff > 0) {
                    return $vacationData;
                }
                return null;
            }

            return null;
        }

        return null;
    }

    public function getSellerId() {
        $shopID = $this->getRequest()->getParam('shop');

        $sellerDataCollection = $this->_sellerData->getCollection()
            ->addFieldToSelect('seller_id')
            ->addFieldToFilter('shop_id',$shopID);
        $sellerID = '';
        if ($sellerDataCollection->getAllIds()) {
            $sellerID = $sellerDataCollection->getData('seller_id');
        }

        return $sellerID;
    }

}
