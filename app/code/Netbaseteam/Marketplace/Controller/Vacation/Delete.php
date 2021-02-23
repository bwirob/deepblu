<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\Marketplace\Controller\Vacation;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;


class Delete extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param FormKeyValidator $formKeyValidator
     * @param Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Marketplace\Model\VacationFactory $vacationFactory,
        \Netbaseteam\Marketplace\Model\SellerdataFactory $sellerdataFactory
    )
    {
        $this->_customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_date = $date;
        $this->_registry = $registry;
        $this->vacationFactory = $vacationFactory;
        $this->sellerdataFactory = $sellerdataFactory;
        parent::__construct(
            $context
        );
    }

    /**
     * Save Seller Vacation Informations.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $shopId = $this->getRequest()->getParam('shop');
        $seller = $this->sellerdataFactory->create()->getCollection()->addFieldToFilter('shop_id', $shopId);

        $sellerId = '';
        foreach ($seller as $item) {
            $sellerId = $item->getSellerId();
        }

        $vacation = $this->vacationFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);
        $vacationId = '';
        try {
            foreach ($vacation as $item) {
                $vacationId = $item->getId();
            }
            $this->vacationFactory->create()->load($vacationId)->delete();
            $this->_redirect("marketplace/seller/profile/shop/$shopId");
            return;
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath(
            "marketplace/seller/profile/shop/$shopId",
            ['_secure' => $this->getRequest()->isSecure()]
        );

    }
}
