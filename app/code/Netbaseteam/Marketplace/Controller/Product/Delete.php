<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\Marketplace\Controller\Product;

use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\Framework\App\Action\Action
{

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Remove item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Netbaseteam\Marketplace\Helper\Data'
        );
        $isPartner = $helper->getSellerId();
        if ($isPartner) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            $productId = (int)$this->getRequest()->getParam('id');
            $items = $this->_objectManager->create('Netbaseteam\Marketplace\Model\Product')
                ->getCollection()
                ->addFieldToFilter('product_id', $productId);
            foreach ($items as $item) {
                $id = $item->getId();
            }
            $item = $this->_objectManager->create(
                'Netbaseteam\Marketplace\Model\Product'
            )->load($id);

            $product = $this->productRepository->getById($productId);

            if (!$item->getId()) {
                throw new NotFoundException(__('Page not found.'));
            }

            try {
                $item->delete();
                $this->productRepository->delete($product);
                $this->messageManager->addSuccess(
                    __('Product have been deleted.')
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError(
                    __('We can\'t delete the product from Marketplace right now because of an error: %1.', $e->getMessage())
                );
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t delete the product from Marketplace right now.'));
            }

            $request = $this->getRequest();
            $refererUrl = (string)$request->getServer('HTTP_REFERER');
            $url = (string)$request->getParam(\Magento\Framework\App\Response\RedirectInterface::PARAM_NAME_REFERER_URL);
            if ($url) {
                $refererUrl = $url;
            }
            if ($request->getParam(\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED) && $refererUrl) {
                $redirectUrl = $refererUrl;
            } else {
                $redirectUrl = $this->_redirect->getRedirectUrl($this->_url->getUrl('*/*'));
            }
            $resultRedirect->setUrl($redirectUrl);
            return $resultRedirect;
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/registry',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }

    }
}
