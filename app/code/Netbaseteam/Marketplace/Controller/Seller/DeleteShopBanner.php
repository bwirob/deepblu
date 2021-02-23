<?php

namespace Netbaseteam\Marketplace\Controller\Seller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Marketplace Seller Profile controller.
 */
class DeleteShopBanner extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Marketplace Seller's Profile Page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $id = '';
            $sellerId = $this->_customerSession->getCustomerId();
            $collection = $this->_objectManager->create(
                'Netbaseteam\Marketplace\Model\Sellerdata'
            )->getCollection()
                ->addFieldToFilter(
                    'seller_id',
                    $sellerId
                );
            foreach ($collection as $item) {
                $id = $item->getId();
            }
            if ($id != '') {
                $sellerData = $this->_objectManager->create(
                    'Netbaseteam\Marketplace\Model\Sellerdata'
                )->load($id);
                $sellerData->setShopBanner('');
                $sellerData->save();
            }
            return $this->resultRedirectFactory->create()->setPath(
                '*/*/editProfile',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        } catch (\Exception $e) {
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')
                    ->jsonEncode($e->getMessage())
            );

            return $this->resultRedirectFactory->create()->setPath(
                '*/*/editProfile',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
